<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $categoryId = (string) $request->query('category_id', '');
        $status = (string) $request->query('status', '');

        $query = News::query()->with(['category', 'creator']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('konten', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if ($categoryId !== '') {
            $query->where('news_category_id', (int) $categoryId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $news = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $categories = NewsCategory::query()->orderBy('nama')->get(['id', 'nama']);

        return view('admin.news.index', [
            'news' => $news,
            'categories' => $categories,
            'filters' => [
                'q' => $q,
                'category_id' => $categoryId,
                'status' => $status,
            ],
        ]);
    }

    public function create()
    {
        $categories = NewsCategory::query()->orderBy('nama')->get(['id', 'nama']);

        return view('admin.news.create', [
            'categories' => $categories,
        ]);
    }

    public function edit(News $news)
    {
        $categories = NewsCategory::query()->orderBy('nama')->get(['id', 'nama']);

        return view('admin.news.edit', [
            'news' => $news,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateForm($request);

        $gambarPath = null;

        try {
            DB::transaction(function () use ($data, $request, &$gambarPath) {
                $slug = $this->makeUniqueSlug($data['slug'] ?? '', $data['judul']);

                if ($request->hasFile('gambar_utama')) {
                    $gambarPath = $this->storeAndOptimizeImage($request->file('gambar_utama'));
                }

                $status = $data['status'];
                $publishedAt = $status === 'published' ? now() : null;

                News::create([
                    'news_category_id' => (int) $data['news_category_id'],
                    'judul' => $data['judul'],
                    'slug' => $slug,
                    'konten' => $this->sanitizeHtml($data['konten']),
                    'gambar_utama' => $gambarPath,
                    'status' => $status,
                    'published_at' => $publishedAt,
                    'meta_description' => $data['meta_description'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                    'created_by' => $this->resolveCreatorId(),
                ]);
            });

            return redirect()
                ->route('admin.news.index')
                ->with('success', 'Berita berhasil dibuat.');
        } catch (Throwable $e) {
            if ($gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat berita. Silakan coba lagi.');
        }
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $data = $this->validateForm($request, $news->id);

        $oldImage = $news->gambar_utama;
        $newImage = null;

        try {
            DB::transaction(function () use ($data, $request, $news, &$newImage) {
                $slug = $this->makeUniqueSlug($data['slug'] ?? '', $data['judul'], $news->id);

                $news->fill([
                    'news_category_id' => (int) $data['news_category_id'],
                    'judul' => $data['judul'],
                    'slug' => $slug,
                    'konten' => $this->sanitizeHtml($data['konten']),
                    'status' => $data['status'],
                    'meta_description' => $data['meta_description'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                ]);

                if ($news->status === 'published') {
                    if (! $news->published_at) {
                        $news->published_at = now();
                    }
                } else {
                    $news->published_at = null;
                }

                if ($request->hasFile('gambar_utama')) {
                    $newImage = $this->storeAndOptimizeImage($request->file('gambar_utama'));
                    $news->gambar_utama = $newImage;
                }

                $news->save();
            });

            if ($newImage && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return redirect()
                ->route('admin.news.index')
                ->with('success', 'Berita berhasil diperbarui.');
        } catch (Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui berita. Silakan coba lagi.');
        }
    }

    public function destroy(News $news): RedirectResponse
    {
        $path = $news->gambar_utama;

        try {
            $news->delete();

            if ($path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->route('admin.news.index')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', 'Gagal menghapus berita. Silakan coba lagi.');
        }
    }

    private function validateForm(Request $request, ?int $ignoreId = null): array
    {
        $uniqueSlug = 'unique:news,slug';
        if ($ignoreId) {
            $uniqueSlug .= ','.$ignoreId;
        }

        return $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $uniqueSlug],
            'konten' => 'required|string',
            'news_category_id' => 'required|integer|exists:news_categories,id',
            'status' => 'required|in:draft,published',
            'gambar_utama' => 'nullable|image|max:2048',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
        ]);
    }

    private function makeUniqueSlug(string $rawSlug, string $judul, ?int $ignoreId = null): string
    {
        $base = trim($rawSlug) !== '' ? $rawSlug : Str::slug($judul);
        $base = $base !== '' ? $base : Str::random(10);

        $slug = $base;
        $i = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$i;
            $i += 1;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = News::query()->where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    private function storeAndOptimizeImage($file): string
    {
        $path = $file->store('news', 'public');
        $this->optimizeStoredImage($path);

        return $path;
    }

    private function optimizeStoredImage(string $path): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            return;
        }

        $abs = $disk->path($path);
        $raw = @file_get_contents($abs);
        if ($raw === false) {
            return;
        }

        $info = @getimagesizefromstring($raw);
        if (! is_array($info) || empty($info[0]) || empty($info[1]) || empty($info['mime'])) {
            return;
        }

        $width = (int) $info[0];
        $height = (int) $info[1];
        $mime = (string) $info['mime'];

        $maxWidth = 1400;
        if ($width <= 0 || $height <= 0 || $width <= $maxWidth) {
            return;
        }

        $src = @imagecreatefromstring($raw);
        if (! is_resource($src) && ! ($src instanceof \GdImage)) {
            return;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) round(($height / $width) * $newWidth);
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        if (! $dst) {
            return;
        }

        if (in_array($mime, ['image/png', 'image/webp'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            imagejpeg($dst, $abs, 82);
        } elseif ($mime === 'image/png') {
            imagepng($dst, $abs, 7);
        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($dst, $abs, 82);
        }

        imagedestroy($src);
        imagedestroy($dst);
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html) ?? '';
        $html = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $html) ?? '';
        $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html) ?? '';
        $html = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $html) ?? '';
        $html = preg_replace('/\son\w+\s*=\s*[^\s>]+/i', '', $html) ?? '';
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:[^"\']*\2/i', '$1=$2#$2', $html) ?? '';

        return trim($html);
    }

    private function resolveCreatorId(): int
    {
        $id = Auth::id();
        if ($id) {
            return (int) $id;
        }

        $adminId = User::query()->where('role', 'admin')->value('id');
        if ($adminId) {
            return (int) $adminId;
        }

        $anyId = User::query()->value('id');
        if ($anyId) {
            return (int) $anyId;
        }

        $user = User::query()->create([
            'nama' => 'System Admin',
            'email' => 'system@webinar.local',
            'password' => Hash::make(Str::random(64)),
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);

        return (int) $user->id;
    }
}
