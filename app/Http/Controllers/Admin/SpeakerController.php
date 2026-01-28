<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speaker;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SpeakerController extends Controller
{
    public function image(Speaker $speaker)
    {
        $raw = trim((string) ($speaker->foto ?? ''));

        if ($raw === '') {
            abort(404);
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return redirect()->away($raw);
        }

        $path = ltrim($raw, '/');
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

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $query = Speaker::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('jabatan', 'like', "%{$q}%")
                    ->orWhere('perusahaan', 'like', "%{$q}%");
            });
        }

        $speakers = $query
            ->orderBy('urutan')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.speakers.index', [
            'speakers' => $speakers,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.speakers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateForm($request);

        $fotoPath = null;

        try {
            DB::transaction(function () use ($request, $data, &$fotoPath) {
                if ($request->hasFile('foto_file')) {
                    $fotoPath = $request->file('foto_file')->store('speakers', 'public');
                }

                $foto = $fotoPath ?: ($data['foto'] ?: null);

                Speaker::create([
                    'nama' => $data['nama'],
                    'jabatan' => $data['jabatan'],
                    'perusahaan' => $data['perusahaan'],
                    'linkedin_url' => $data['linkedin_url'] ?: null,
                    'foto' => $foto,
                    'urutan' => (int) $data['urutan'],
                    'is_active' => (bool) $data['is_active'],
                ]);
            });

            return redirect()
                ->route('speakers.index')
                ->with('success', 'Speaker berhasil dibuat.');
        } catch (Throwable $e) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat speaker. Silakan coba lagi.');
        }
    }

    public function show(Speaker $speaker): View
    {
        return view('admin.speakers.show', [
            'speaker' => $speaker,
        ]);
    }

    public function edit(Speaker $speaker): View
    {
        return view('admin.speakers.edit', [
            'speaker' => $speaker,
        ]);
    }

    public function update(Request $request, Speaker $speaker): RedirectResponse
    {
        $data = $this->validateForm($request, true);

        $oldFoto = $speaker->foto;
        $newFotoPath = null;

        try {
            DB::transaction(function () use ($request, $speaker, $data, &$newFotoPath) {
                if ($request->hasFile('foto_file')) {
                    $newFotoPath = $request->file('foto_file')->store('speakers', 'public');
                }

                $foto = $speaker->foto;

                if ((bool) $data['hapus_foto']) {
                    $foto = null;
                }

                if ($data['foto'] !== '' && ! ((bool) $data['hapus_foto'])) {
                    $foto = $data['foto'];
                }

                if ($newFotoPath) {
                    $foto = $newFotoPath;
                }

                $speaker->update([
                    'nama' => $data['nama'],
                    'jabatan' => $data['jabatan'],
                    'perusahaan' => $data['perusahaan'],
                    'linkedin_url' => $data['linkedin_url'] ?: null,
                    'foto' => $foto ?: null,
                    'urutan' => (int) $data['urutan'],
                    'is_active' => (bool) $data['is_active'],
                ]);
            });

            $shouldDeleteOld = ((bool) $data['hapus_foto']) || $newFotoPath;
            if ($shouldDeleteOld) {
                $oldPath = $this->resolvePublicDiskPath($oldFoto);
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            return redirect()
                ->route('speakers.index')
                ->with('success', 'Speaker berhasil diperbarui.');
        } catch (Throwable $e) {
            if ($newFotoPath) {
                Storage::disk('public')->delete($newFotoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui speaker. Silakan coba lagi.');
        }
    }

    public function destroy(Speaker $speaker): RedirectResponse
    {
        $oldFoto = $speaker->foto;

        try {
            DB::transaction(function () use ($speaker) {
                $speaker->delete();
            });

            $oldPath = $this->resolvePublicDiskPath($oldFoto);
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            return redirect()
                ->route('speakers.index')
                ->with('success', 'Speaker berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal menghapus speaker. Silakan coba lagi.');
        }
    }

    private function validateForm(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'nama' => 'required|string|max:150',
            'jabatan' => 'required|string|max:150',
            'perusahaan' => 'required|string|max:150',
            'linkedin_url' => 'nullable|url|max:2048',
            'foto' => 'nullable|string|max:2048',
            'foto_file' => 'nullable|image|max:2048',
            'urutan' => 'required|integer|min:0|max:1000000',
            'is_active' => 'required|boolean',
        ];

        if ($isUpdate) {
            $rules['hapus_foto'] = 'required|boolean';
        }

        $data = $request->validate($rules);

        return [
            'nama' => trim((string) $data['nama']),
            'jabatan' => trim((string) $data['jabatan']),
            'perusahaan' => trim((string) $data['perusahaan']),
            'linkedin_url' => trim((string) ($data['linkedin_url'] ?? '')),
            'foto' => trim((string) ($data['foto'] ?? '')),
            'urutan' => (int) $data['urutan'],
            'is_active' => (bool) $data['is_active'],
            'hapus_foto' => (bool) ($data['hapus_foto'] ?? false),
        ];
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

        $path = ltrim($raw, '/');
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
