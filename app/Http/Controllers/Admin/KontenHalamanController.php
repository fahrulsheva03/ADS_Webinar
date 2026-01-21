<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KontenHalamanController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function home(): View
    {
        $page = 'home';
        $fields = $this->homeFields();

        $existing = PageContent::query()
            ->where('page', $page)
            ->get()
            ->keyBy(fn (PageContent $row) => "{$row->section}.{$row->key}");

        $values = [];
        foreach ($fields as $field) {
            $compound = "{$field['section']}.{$field['key']}";
            $values[$field['section']][$field['key']] = (string) optional($existing->get($compound))->value;
        }

        $fieldsBySection = collect($fields)->groupBy('section');

        return view('admin.konten-halaman.index', [
            'page' => $page,
            'fieldsBySection' => $fieldsBySection,
            'values' => $values,
        ]);
    }

    public function updateHome(Request $request): RedirectResponse
    {
        $page = 'home';
        $fields = $this->homeFields();

        $data = $request->validate([
            'contents' => 'required|array',
            'contents.*' => 'array',
            'contents.*.*' => 'nullable|string|max:65535',
        ]);

        DB::transaction(function () use ($data, $fields, $page) {
            foreach ($fields as $field) {
                $section = (string) $field['section'];
                $key = (string) $field['key'];
                $value = (string) ($data['contents'][$section][$key] ?? '');

                PageContent::query()->updateOrCreate(
                    [
                        'page' => $page,
                        'section' => $section,
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        });

        return back()->with('success', 'Konten berhasil disimpan.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page' => 'required|string|max:50',
            'section' => 'required|string|max:50',
            'key' => 'required|string|max:100',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $page = Str::slug($data['page'], '-');
        $section = Str::slug($data['section'], '-');
        $key = Str::slug($data['key'], '_');

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $stamp = now()->format('Ymd_His');
        $rand = Str::lower(Str::random(6));
        $fileName = "{$key}_{$stamp}_{$rand}.{$ext}";

        $relativeDir = "assetsAdmin/uploads/konten-halaman/{$page}/{$section}";
        $absoluteDir = public_path($relativeDir);
        File::ensureDirectoryExists($absoluteDir);

        $file->move($absoluteDir, $fileName);

        $path = "{$relativeDir}/{$fileName}";

        return response()->json([
            'message' => 'Upload berhasil.',
            'data' => [
                'path' => $path,
                'url' => asset($path),
            ],
        ]);
    }

    private function homeFields(): array
    {
        return [
            [
                'section' => 'banner',
                'key' => 'slide_1_image',
                'label' => 'Banner - Gambar',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'banner',
                'key' => 'slide_1_date',
                'label' => 'Banner - Tanggal',
                'type' => 'text',
                'placeholder' => 'Contoh: 21-25 Nov 2026',
            ],
            [
                'section' => 'banner',
                'key' => 'slide_1_title',
                'label' => 'Banner - Judul',
                'type' => 'text',
            ],
            [
                'section' => 'banner',
                'key' => 'slide_1_location',
                'label' => 'Banner - Lokasi',
                'type' => 'text',
            ],
            [
                'section' => 'banner',
                'key' => 'slide_1_button_text',
                'label' => 'Banner - Teks Tombol',
                'type' => 'text',
            ],
            [
                'section' => 'banner',
                'key' => 'slide_1_button_url',
                'label' => 'Banner - URL Tombol',
                'type' => 'url',
                'placeholder' => 'https://',
            ],

            [
                'section' => 'journey',
                'key' => 'title',
                'label' => 'Journey - Judul',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'description',
                'label' => 'Journey - Deskripsi',
                'type' => 'textarea',
                'rows' => 6,
            ],
            [
                'section' => 'journey',
                'key' => 'button_text',
                'label' => 'Journey - Teks Tombol',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'button_url',
                'label' => 'Journey - URL Tombol',
                'type' => 'url',
                'placeholder' => 'https://',
            ],
            [
                'section' => 'journey',
                'key' => 'video_bg_image',
                'label' => 'Journey - Gambar Video',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'journey',
                'key' => 'video_url',
                'label' => 'Journey - URL Video (YouTube embed)',
                'type' => 'url',
                'placeholder' => 'https://www.youtube.com/embed/…',
            ],

            [
                'section' => 'journey',
                'key' => 'digital_image',
                'label' => 'Journey - Gambar Digital Conference',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_1_value',
                'label' => 'Journey - Counter 1 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_1_label',
                'label' => 'Journey - Counter 1 (label)',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_2_value',
                'label' => 'Journey - Counter 2 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_2_label',
                'label' => 'Journey - Counter 2 (label)',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_3_value',
                'label' => 'Journey - Counter 3 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_3_label',
                'label' => 'Journey - Counter 3 (label)',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_4_value',
                'label' => 'Journey - Counter 4 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_4_suffix',
                'label' => 'Journey - Counter 4 (suffix, mis: K)',
                'type' => 'text',
                'placeholder' => 'Contoh: K',
            ],
            [
                'section' => 'journey',
                'key' => 'counter_4_label',
                'label' => 'Journey - Counter 4 (label)',
                'type' => 'text',
            ],

            [
                'section' => 'journey',
                'key' => 'digital_title',
                'label' => 'Journey - Judul Digital',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'digital_badge',
                'label' => 'Journey - Badge Digital',
                'type' => 'text',
                'placeholder' => 'Contoh: NOV 21-25, 2026',
            ],
            [
                'section' => 'journey',
                'key' => 'digital_button_text',
                'label' => 'Journey - Teks Tombol Digital',
                'type' => 'text',
            ],
            [
                'section' => 'journey',
                'key' => 'digital_button_url',
                'label' => 'Journey - URL Tombol Digital',
                'type' => 'url',
                'placeholder' => 'https://',
            ],
        ];
    }
}
