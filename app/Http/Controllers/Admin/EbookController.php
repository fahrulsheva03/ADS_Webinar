<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EbookStoreRequest;
use App\Http\Requests\Admin\EbookUpdateRequest;
use App\Models\Ebook;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EbookController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $author = trim((string) $request->query('author', ''));
        $status = trim((string) $request->query('status', ''));

        $query = Ebook::query();

        if ($q !== '') {
            $query->where('title', 'like', "%{$q}%");
        }

        if ($author !== '') {
            $query->where('author', 'like', "%{$author}%");
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $ebooks = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.ebooks.index', [
            'ebooks' => $ebooks,
            'filters' => [
                'q' => $q,
                'author' => $author,
                'status' => $status,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.ebooks.create');
    }

    public function store(EbookStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $coverPath = null;
        $pdfPath = null;

        try {
            DB::transaction(function () use ($data, $request, &$coverPath, &$pdfPath) {
                $coverPath = $request->file('cover_image')->store('ebooks/covers', 'public');
                $pdfPath = $request->file('pdf_file')->store('ebooks/pdf', 'public');

                Ebook::create([
                    'title' => trim((string) $data['title']),
                    'author' => trim((string) $data['author']),
                    'description' => isset($data['description']) ? trim((string) $data['description']) : null,
                    'cover_image' => $coverPath,
                    'pdf_file' => $pdfPath,
                    'price' => $data['price'],
                    'stock' => (int) $data['stock'],
                    'is_active' => (bool) $data['is_active'],
                ]);
            });

            return redirect()
                ->route('admin.ebooks.index')
                ->with('success', 'E-book berhasil dibuat.');
        } catch (Throwable $e) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }
            if ($pdfPath) {
                Storage::disk('public')->delete($pdfPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat e-book. Silakan coba lagi.');
        }
    }

    public function show(Ebook $ebook): View
    {
        return view('admin.ebooks.show', [
            'ebook' => $ebook,
        ]);
    }

    public function edit(Ebook $ebook): View
    {
        return view('admin.ebooks.edit', [
            'ebook' => $ebook,
        ]);
    }

    public function update(EbookUpdateRequest $request, Ebook $ebook): RedirectResponse
    {
        $data = $request->validated();

        $oldCover = $ebook->cover_image;
        $oldPdf = $ebook->pdf_file;
        $newCoverPath = null;
        $newPdfPath = null;

        try {
            DB::transaction(function () use ($data, $request, $ebook, &$newCoverPath, &$newPdfPath) {
                if ($request->hasFile('cover_image')) {
                    $newCoverPath = $request->file('cover_image')->store('ebooks/covers', 'public');
                }
                if ($request->hasFile('pdf_file')) {
                    $newPdfPath = $request->file('pdf_file')->store('ebooks/pdf', 'public');
                }

                $ebook->update([
                    'title' => trim((string) $data['title']),
                    'author' => trim((string) $data['author']),
                    'description' => isset($data['description']) ? trim((string) $data['description']) : null,
                    'cover_image' => $newCoverPath ?: $ebook->cover_image,
                    'pdf_file' => $newPdfPath ?: $ebook->pdf_file,
                    'price' => $data['price'],
                    'stock' => (int) $data['stock'],
                    'is_active' => (bool) $data['is_active'],
                ]);
            });

            if ($newCoverPath) {
                $oldPath = $this->resolvePublicDiskPath($oldCover);
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            if ($newPdfPath) {
                $oldPath = $this->resolvePublicDiskPath($oldPdf);
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            return redirect()
                ->route('admin.ebooks.index')
                ->with('success', 'E-book berhasil diperbarui.');
        } catch (Throwable $e) {
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }
            if ($newPdfPath) {
                Storage::disk('public')->delete($newPdfPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui e-book. Silakan coba lagi.');
        }
    }

    public function destroy(Ebook $ebook): RedirectResponse
    {
        try {
            DB::transaction(function () use ($ebook) {
                $ebook->delete();
            });

            return redirect()
                ->route('admin.ebooks.index')
                ->with('success', 'E-book berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal menghapus e-book. Silakan coba lagi.');
        }
    }

    public function cover(Ebook $ebook)
    {
        $raw = trim((string) ($ebook->cover_image ?? ''));

        if ($raw === '') {
            abort(404);
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return redirect()->away($raw);
        }

        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        abort_if(str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        abort_if(! $disk->exists($path), 404);

        return response()->file($disk->path($path));
    }

    public function pdf(Ebook $ebook)
    {
        $raw = trim((string) ($ebook->pdf_file ?? ''));

        if ($raw === '') {
            abort(404);
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return redirect()->away($raw);
        }

        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        abort_if(str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        abort_if(! $disk->exists($path), 404);

        return response()->file($disk->path($path));
    }

    private function resolvePublicDiskPath(?string $raw): ?string
    {
        $raw = trim((string) ($raw ?? ''));
        if ($raw === '') {
            return null;
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (str_contains($path, '..')) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return $path;
    }
}
