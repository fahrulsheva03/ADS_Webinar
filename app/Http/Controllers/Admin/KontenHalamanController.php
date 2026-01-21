<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KontenHalamanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
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

    private function homeFields(): array
    {
        return [
            ['section' => 'banner', 'key' => 'slide_1_image', 'label' => 'Banner - Gambar (path public)'],
            ['section' => 'banner', 'key' => 'slide_1_date', 'label' => 'Banner - Tanggal'],
            ['section' => 'banner', 'key' => 'slide_1_title', 'label' => 'Banner - Judul'],
            ['section' => 'banner', 'key' => 'slide_1_location', 'label' => 'Banner - Lokasi'],
            ['section' => 'banner', 'key' => 'slide_1_button_text', 'label' => 'Banner - Teks Tombol'],
            ['section' => 'banner', 'key' => 'slide_1_button_url', 'label' => 'Banner - URL Tombol'],

            ['section' => 'journey', 'key' => 'title', 'label' => 'Journey - Judul'],
            ['section' => 'journey', 'key' => 'description', 'label' => 'Journey - Deskripsi'],
            ['section' => 'journey', 'key' => 'button_text', 'label' => 'Journey - Teks Tombol'],
            ['section' => 'journey', 'key' => 'button_url', 'label' => 'Journey - URL Tombol'],
            ['section' => 'journey', 'key' => 'video_bg_image', 'label' => 'Journey - Gambar Video (path public)'],
            ['section' => 'journey', 'key' => 'video_url', 'label' => 'Journey - URL Video (YouTube embed)'],

            ['section' => 'journey', 'key' => 'digital_image', 'label' => 'Journey - Gambar Digital Conference (path public)'],
            ['section' => 'journey', 'key' => 'counter_1_value', 'label' => 'Journey - Counter 1 (angka)'],
            ['section' => 'journey', 'key' => 'counter_1_label', 'label' => 'Journey - Counter 1 (label)'],
            ['section' => 'journey', 'key' => 'counter_2_value', 'label' => 'Journey - Counter 2 (angka)'],
            ['section' => 'journey', 'key' => 'counter_2_label', 'label' => 'Journey - Counter 2 (label)'],
            ['section' => 'journey', 'key' => 'counter_3_value', 'label' => 'Journey - Counter 3 (angka)'],
            ['section' => 'journey', 'key' => 'counter_3_label', 'label' => 'Journey - Counter 3 (label)'],
            ['section' => 'journey', 'key' => 'counter_4_value', 'label' => 'Journey - Counter 4 (angka)'],
            ['section' => 'journey', 'key' => 'counter_4_suffix', 'label' => 'Journey - Counter 4 (suffix, mis: K)'],
            ['section' => 'journey', 'key' => 'counter_4_label', 'label' => 'Journey - Counter 4 (label)'],

            ['section' => 'journey', 'key' => 'digital_title', 'label' => 'Journey - Judul Digital'],
            ['section' => 'journey', 'key' => 'digital_badge', 'label' => 'Journey - Badge Digital (mis: NOV 21-25, 2024)'],
            ['section' => 'journey', 'key' => 'digital_button_text', 'label' => 'Journey - Teks Tombol Digital'],
            ['section' => 'journey', 'key' => 'digital_button_url', 'label' => 'Journey - URL Tombol Digital'],
        ];
    }
}
