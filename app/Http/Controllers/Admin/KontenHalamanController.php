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
        return $this->renderPage('home', $this->homeFields());
    }

    public function about(): View
    {
        return $this->renderPage('about', $this->aboutFields());
    }

    public function updateHome(Request $request): RedirectResponse
    {
        return $this->updatePage($request, 'home', $this->homeFields(), true);
    }

    public function updateAbout(Request $request): RedirectResponse
    {
        return $this->updatePage($request, 'about', $this->aboutFields());
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

    public function pricingCardsStore(Request $request): JsonResponse
    {
        $cards = $this->getExtraPricingCards();

        $card = [
            'id' => (string) Str::uuid(),
            'active' => '0',
            'title' => '',
            'subtitle' => '',
            'currency' => 'USD',
            'price' => '',
            'features' => [],
            'button_text' => '',
            'button_url' => '',
            'badge' => '',
        ];

        $cards[] = $card;
        $this->saveExtraPricingCards($cards);

        return response()->json([
            'message' => 'Card berhasil ditambahkan.',
            'data' => $card,
        ]);
    }

    public function pricingCardsUpdate(Request $request, string $cardId): JsonResponse
    {
        $cards = $this->getExtraPricingCards();

        $idx = collect($cards)->search(fn (array $c) => (string) ($c['id'] ?? '') === $cardId);
        abort_if($idx === false, 404);

        $data = $request->validate([
            'active' => 'nullable|in:0,1',
            'title' => 'nullable|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'currency' => 'nullable|in:USD,IDR,EUR',
            'price' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:80',
            'button_url' => 'nullable|url|max:2048',
            'badge' => 'nullable|string|max:60',
        ]);

        $card = (array) $cards[$idx];

        $next = array_merge($card, $data);
        $next['active'] = isset($next['active']) ? (string) $next['active'] : '0';
        $next['title'] = trim((string) ($next['title'] ?? ''));
        $next['subtitle'] = trim((string) ($next['subtitle'] ?? ''));
        $next['currency'] = trim((string) ($next['currency'] ?? 'USD')) ?: 'USD';
        $next['price'] = isset($next['price']) && $next['price'] !== '' ? (string) $next['price'] : '';
        $next['button_text'] = trim((string) ($next['button_text'] ?? ''));
        $next['button_url'] = trim((string) ($next['button_url'] ?? ''));
        $next['badge'] = trim((string) ($next['badge'] ?? ''));

        $features = $next['features'] ?? [];
        if (! is_array($features)) {
            $features = [];
        }
        $features = collect($features)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();
        $next['features'] = $features;

        if ($next['active'] === '1') {
            $errors = [];

            if ($next['currency'] === '') {
                $errors['currency'] = ['Mata uang wajib diisi saat card aktif.'];
            }
            if ($next['title'] === '') {
                $errors['title'] = ['Judul wajib diisi saat card aktif.'];
            }
            if ($next['subtitle'] === '') {
                $errors['subtitle'] = ['Deskripsi wajib diisi saat card aktif.'];
            }
            if ($next['price'] === '') {
                $errors['price'] = ['Harga wajib diisi saat card aktif.'];
            }
            if (count($features) === 0) {
                $errors['features'] = ['Minimal 1 fitur wajib diisi saat card aktif.'];
            }
            if ($next['button_text'] === '') {
                $errors['button_text'] = ['Teks tombol wajib diisi saat card aktif.'];
            }
            if ($next['button_url'] === '') {
                $errors['button_url'] = ['URL tombol wajib diisi saat card aktif.'];
            }

            if (count($errors) > 0) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $errors,
                ], 422);
            }
        }

        $cards[$idx] = $next;
        $this->saveExtraPricingCards($cards);

        return response()->json([
            'message' => 'Card berhasil diperbarui.',
            'data' => $next,
        ]);
    }

    public function pricingCardsDestroy(string $cardId): JsonResponse
    {
        $cards = $this->getExtraPricingCards();
        $filtered = collect($cards)->reject(fn (array $c) => (string) ($c['id'] ?? '') === $cardId)->values()->all();

        if (count($filtered) === count($cards)) {
            abort(404);
        }

        $this->saveExtraPricingCards($filtered);

        return response()->json([
            'message' => 'Card berhasil dihapus.',
        ]);
    }

    public function faqItemsStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question' => 'nullable|string|max:255',
            'answer' => 'nullable|string|max:65535',
            'order' => 'nullable|integer|min:0',
            'active' => 'nullable|in:0,1',
        ]);

        $items = $this->getFaqItems();
        $nextOrder = 1;
        if (count($items) > 0) {
            $maxOrder = collect($items)->map(fn (array $v) => (int) ($v['order'] ?? 0))->max();
            $nextOrder = ((int) ($maxOrder ?? 0)) + 1;
            if ($nextOrder < 1) {
                $nextOrder = 1;
            }
        }

        $itemId = (string) Str::uuid();
        $item = [
            'id' => $itemId,
            'question' => trim((string) ($data['question'] ?? '')),
            'answer' => trim((string) ($data['answer'] ?? '')),
            'order' => isset($data['order']) ? (int) $data['order'] : $nextOrder,
            'active' => isset($data['active']) ? (string) $data['active'] : '1',
        ];

        $items[] = $item;
        $items = $this->normalizeFaqItemsOrder($items);
        $this->saveFaqItems($items);

        $stored = collect($items)->firstWhere('id', $itemId);

        return response()->json([
            'message' => 'FAQ berhasil ditambahkan.',
            'data' => $stored ?? $item,
        ]);
    }

    public function faqItemsDestroy(string $faqId): JsonResponse
    {
        $items = $this->getFaqItems();
        $filtered = collect($items)->reject(fn (array $c) => (string) ($c['id'] ?? '') === $faqId)->values()->all();

        if (count($filtered) === count($items)) {
            abort(404);
        }

        $filtered = $this->normalizeFaqItemsOrder($filtered);
        $this->saveFaqItems($filtered);

        return response()->json([
            'message' => 'FAQ berhasil dihapus.',
        ]);
    }

    public function faqItemsSync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|string|max:100',
            'items.*.question' => 'nullable|string|max:255',
            'items.*.answer' => 'nullable|string|max:65535',
            'items.*.order' => 'nullable|integer|min:0',
            'items.*.active' => 'nullable|in:0,1',
        ]);

        $items = collect($data['items'] ?? [])
            ->filter(fn ($v) => is_array($v) && isset($v['id']))
            ->map(function (array $v) {
                return [
                    'id' => trim((string) ($v['id'] ?? '')),
                    'question' => trim((string) ($v['question'] ?? '')),
                    'answer' => trim((string) ($v['answer'] ?? '')),
                    'order' => isset($v['order']) ? (int) $v['order'] : 0,
                    'active' => isset($v['active']) ? (string) $v['active'] : '1',
                ];
            })
            ->filter(fn (array $v) => $v['id'] !== '')
            ->values()
            ->all();

        $items = $this->normalizeFaqItemsOrder($items);
        $this->saveFaqItems($items);

        return response()->json([
            'message' => 'FAQ berhasil disimpan.',
            'data' => $items,
        ]);
    }

    private function normalizeFaqItemsOrder(array $items): array
    {
        $indexed = [];
        foreach (array_values($items) as $idx => $item) {
            if (! is_array($item)) {
                continue;
            }
            $indexed[] = ['__idx' => $idx] + $item;
        }

        usort($indexed, function (array $a, array $b) {
            $ao = (int) ($a['order'] ?? 0);
            $bo = (int) ($b['order'] ?? 0);

            if ($ao === $bo) {
                return ((int) ($a['__idx'] ?? 0)) <=> ((int) ($b['__idx'] ?? 0));
            }

            return $ao <=> $bo;
        });

        $out = [];
        foreach (array_values($indexed) as $i => $item) {
            unset($item['__idx']);
            $item['order'] = $i + 1;
            $out[] = $item;
        }

        return $out;
    }

    private function renderPage(string $page, array $fields): View
    {
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

    private function updatePage(Request $request, string $page, array $fields, bool $withPricingRules = false): RedirectResponse
    {
        $rules = [
            'contents' => 'required|array',
            'contents.*' => 'array',
        ];

        foreach ($fields as $field) {
            $section = (string) $field['section'];
            $key = (string) $field['key'];
            $type = (string) ($field['type'] ?? 'textarea');

            $rule = match ($type) {
                'url' => 'nullable|url|max:2048',
                'number' => 'nullable|numeric|min:0',
                'image' => 'nullable|string|max:2048',
                'text' => 'nullable|string|max:255',
                default => 'nullable|string|max:65535',
            };

            $rules["contents.{$section}.{$key}"] = $rule;
        }

        if ($withPricingRules) {
            $pricingCards = ['silver', 'gold', 'premium'];
            foreach ($pricingCards as $card) {
                $activeKey = "{$card}_active";
                $currencyKey = "{$card}_currency";

                $rules["contents.pricing.{$activeKey}"] = 'nullable|in:0,1';
                $rules["contents.pricing.{$currencyKey}"] = "nullable|in:USD,IDR,EUR|required_if:contents.pricing.{$activeKey},1";
                $rules["contents.pricing.{$card}_title"] = "nullable|string|max:150|required_if:contents.pricing.{$activeKey},1";
                $rules["contents.pricing.{$card}_subtitle"] = "nullable|string|max:255|required_if:contents.pricing.{$activeKey},1";
                $rules["contents.pricing.{$card}_price"] = "nullable|numeric|min:0|required_if:contents.pricing.{$activeKey},1";
                $rules["contents.pricing.{$card}_features"] = "nullable|string|max:65535|required_if:contents.pricing.{$activeKey},1";
                $rules["contents.pricing.{$card}_button_text"] = "nullable|string|max:80|required_if:contents.pricing.{$activeKey},1";
                $rules["contents.pricing.{$card}_button_url"] = "nullable|url|max:2048|required_if:contents.pricing.{$activeKey},1";
            }

            $rules['contents.pricing.gold_badge'] = 'nullable|string|max:60';
        }

        $data = $request->validate($rules);

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

    private function getExtraPricingCards(): array
    {
        $row = PageContent::query()
            ->where('page', 'home')
            ->where('section', 'pricing')
            ->where('key', 'extra_cards')
            ->first();

        $raw = (string) optional($row)->value;
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->filter(fn ($v) => is_array($v) && isset($v['id']))
            ->map(function (array $v) {
                $v['id'] = (string) ($v['id'] ?? '');
                $v['active'] = (string) ($v['active'] ?? '0');
                $v['title'] = (string) ($v['title'] ?? '');
                $v['subtitle'] = (string) ($v['subtitle'] ?? '');
                $v['currency'] = (string) ($v['currency'] ?? 'USD');
                $v['price'] = isset($v['price']) ? (string) $v['price'] : '';
                $v['features'] = isset($v['features']) && is_array($v['features']) ? $v['features'] : [];
                $v['button_text'] = (string) ($v['button_text'] ?? '');
                $v['button_url'] = (string) ($v['button_url'] ?? '');
                $v['badge'] = (string) ($v['badge'] ?? '');

                return $v;
            })
            ->values()
            ->all();
    }

    private function saveExtraPricingCards(array $cards): void
    {
        $value = json_encode(array_values($cards), JSON_UNESCAPED_UNICODE);
        $value = $value !== false ? $value : '[]';

        PageContent::query()->updateOrCreate(
            [
                'page' => 'home',
                'section' => 'pricing',
                'key' => 'extra_cards',
            ],
            [
                'value' => $value,
            ]
        );
    }

    private function getFaqItems(): array
    {
        $row = PageContent::query()
            ->where('page', 'home')
            ->where('section', 'faq')
            ->where('key', 'items')
            ->first();

        $raw = (string) optional($row)->value;
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->filter(fn ($v) => is_array($v) && isset($v['id']))
            ->map(function (array $v) {
                $v['id'] = (string) ($v['id'] ?? '');
                $v['question'] = (string) ($v['question'] ?? '');
                $v['answer'] = (string) ($v['answer'] ?? '');
                $v['order'] = (int) ($v['order'] ?? 0);
                $v['active'] = (string) ($v['active'] ?? '1');

                return $v;
            })
            ->values()
            ->all();
    }

    private function saveFaqItems(array $items): void
    {
        $value = json_encode(array_values($items), JSON_UNESCAPED_UNICODE);
        $value = $value !== false ? $value : '[]';

        PageContent::query()->updateOrCreate(
            [
                'page' => 'home',
                'section' => 'faq',
                'key' => 'items',
            ],
            [
                'value' => $value,
            ]
        );
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

            [
                'section' => 'speakers',
                'key' => 'small_text',
                'label' => 'Speakers - Teks Kecil',
                'type' => 'text',
                'placeholder' => 'Contoh: SPEAKERS',
            ],
            [
                'section' => 'speakers',
                'key' => 'title',
                'label' => 'Speakers - Judul',
                'type' => 'text',
            ],

            [
                'section' => 'pricing',
                'key' => 'small_text',
                'label' => 'Ticket Pricing - Teks Kecil',
                'type' => 'text',
                'placeholder' => 'Contoh: TICKET PRICING',
            ],
            [
                'section' => 'pricing',
                'key' => 'title',
                'label' => 'Ticket Pricing - Judul',
                'type' => 'textarea',
                'rows' => 3,
                'placeholder' => "Contoh:\nWe Have Several Options\nfor Tickets",
            ],
            [
                'section' => 'pricing',
                'key' => 'extra_cards',
                'label' => 'Ticket Pricing - Extra Cards (JSON)',
                'type' => 'textarea',
                'rows' => 1,
            ],

            [
                'section' => 'pricing',
                'key' => 'silver_title',
                'label' => 'Ticket Pricing - Paket Silver (judul)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_subtitle',
                'label' => 'Ticket Pricing - Paket Silver (subjudul)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_price',
                'label' => 'Ticket Pricing - Paket Silver (harga)',
                'type' => 'number',
                'placeholder' => 'Contoh: 29',
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_currency',
                'label' => 'Ticket Pricing - Paket Silver (mata uang)',
                'type' => 'text',
                'placeholder' => 'Contoh: USD / IDR / EUR',
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_active',
                'label' => 'Ticket Pricing - Paket Silver (aktif)',
                'type' => 'text',
                'placeholder' => '0 atau 1',
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_features',
                'label' => 'Ticket Pricing - Paket Silver (fitur, per baris)',
                'type' => 'textarea',
                'rows' => 5,
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_button_text',
                'label' => 'Ticket Pricing - Paket Silver (teks tombol)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'silver_button_url',
                'label' => 'Ticket Pricing - Paket Silver (URL tombol)',
                'type' => 'url',
                'placeholder' => 'https://',
            ],

            [
                'section' => 'pricing',
                'key' => 'gold_title',
                'label' => 'Ticket Pricing - Paket Gold (judul)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_subtitle',
                'label' => 'Ticket Pricing - Paket Gold (subjudul)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_price',
                'label' => 'Ticket Pricing - Paket Gold (harga)',
                'type' => 'number',
                'placeholder' => 'Contoh: 45',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_currency',
                'label' => 'Ticket Pricing - Paket Gold (mata uang)',
                'type' => 'text',
                'placeholder' => 'Contoh: USD / IDR / EUR',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_active',
                'label' => 'Ticket Pricing - Paket Gold (aktif)',
                'type' => 'text',
                'placeholder' => '0 atau 1',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_features',
                'label' => 'Ticket Pricing - Paket Gold (fitur, per baris)',
                'type' => 'textarea',
                'rows' => 5,
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_button_text',
                'label' => 'Ticket Pricing - Paket Gold (teks tombol)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_button_url',
                'label' => 'Ticket Pricing - Paket Gold (URL tombol)',
                'type' => 'url',
                'placeholder' => 'https://',
            ],
            [
                'section' => 'pricing',
                'key' => 'gold_badge',
                'label' => 'Ticket Pricing - Paket Gold (badge)',
                'type' => 'text',
                'placeholder' => 'Contoh: RECOMMENDED',
            ],

            [
                'section' => 'pricing',
                'key' => 'premium_title',
                'label' => 'Ticket Pricing - Paket Premium (judul)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_subtitle',
                'label' => 'Ticket Pricing - Paket Premium (subjudul)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_price',
                'label' => 'Ticket Pricing - Paket Premium (harga)',
                'type' => 'number',
                'placeholder' => 'Contoh: 59',
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_currency',
                'label' => 'Ticket Pricing - Paket Premium (mata uang)',
                'type' => 'text',
                'placeholder' => 'Contoh: USD / IDR / EUR',
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_active',
                'label' => 'Ticket Pricing - Paket Premium (aktif)',
                'type' => 'text',
                'placeholder' => '0 atau 1',
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_features',
                'label' => 'Ticket Pricing - Paket Premium (fitur, per baris)',
                'type' => 'textarea',
                'rows' => 5,
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_button_text',
                'label' => 'Ticket Pricing - Paket Premium (teks tombol)',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'premium_button_url',
                'label' => 'Ticket Pricing - Paket Premium (URL tombol)',
                'type' => 'url',
                'placeholder' => 'https://',
            ],

            [
                'section' => 'pricing',
                'key' => 'bottom_text',
                'label' => 'Ticket Pricing - Teks Bawah',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'section' => 'pricing',
                'key' => 'bottom_button_text',
                'label' => 'Ticket Pricing - Teks Tombol Bawah',
                'type' => 'text',
            ],
            [
                'section' => 'pricing',
                'key' => 'bottom_button_url',
                'label' => 'Ticket Pricing - URL Tombol Bawah',
                'type' => 'url',
                'placeholder' => 'https://',
            ],

            [
                'section' => 'registration',
                'key' => 'title',
                'label' => 'Registration - Judul',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'section' => 'registration',
                'key' => 'description',
                'label' => 'Registration - Deskripsi',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'section' => 'registration',
                'key' => 'button_text',
                'label' => 'Registration - Teks Tombol',
                'type' => 'text',
            ],
            [
                'section' => 'registration',
                'key' => 'button_url',
                'label' => 'Registration - URL Tombol',
                'type' => 'url',
                'placeholder' => 'https://',
            ],
            [
                'section' => 'registration',
                'key' => 'image',
                'label' => 'Registration - Gambar',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],

            [
                'section' => 'faq',
                'key' => 'small_text',
                'label' => 'FAQ - Teks Kecil',
                'type' => 'text',
                'placeholder' => 'Contoh: FAQ',
            ],
            [
                'section' => 'faq',
                'key' => 'title',
                'label' => 'FAQ - Judul',
                'type' => 'text',
            ],
            [
                'section' => 'faq',
                'key' => 'items',
                'label' => 'FAQ - Items (JSON)',
                'type' => 'textarea',
                'rows' => 1,
            ],
            [
                'section' => 'faq',
                'key' => 'q1',
                'label' => 'FAQ - Pertanyaan 1',
                'type' => 'text',
            ],
            [
                'section' => 'faq',
                'key' => 'a1',
                'label' => 'FAQ - Jawaban 1',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'section' => 'faq',
                'key' => 'q2',
                'label' => 'FAQ - Pertanyaan 2',
                'type' => 'text',
            ],
            [
                'section' => 'faq',
                'key' => 'a2',
                'label' => 'FAQ - Jawaban 2',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'section' => 'faq',
                'key' => 'q3',
                'label' => 'FAQ - Pertanyaan 3',
                'type' => 'text',
            ],
            [
                'section' => 'faq',
                'key' => 'a3',
                'label' => 'FAQ - Jawaban 3',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'section' => 'faq',
                'key' => 'q4',
                'label' => 'FAQ - Pertanyaan 4',
                'type' => 'text',
            ],
            [
                'section' => 'faq',
                'key' => 'a4',
                'label' => 'FAQ - Jawaban 4',
                'type' => 'textarea',
                'rows' => 4,
            ],
            [
                'section' => 'faq',
                'key' => 'q5',
                'label' => 'FAQ - Pertanyaan 5',
                'type' => 'text',
            ],
            [
                'section' => 'faq',
                'key' => 'a5',
                'label' => 'FAQ - Jawaban 5',
                'type' => 'textarea',
                'rows' => 4,
            ],
        ];
    }

    private function aboutFields(): array
    {
        return [
            [
                'section' => 'banner',
                'key' => 'title',
                'label' => 'Banner - Judul',
                'type' => 'text',
            ],
            [
                'section' => 'banner',
                'key' => 'description',
                'label' => 'Banner - Deskripsi',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'section' => 'about',
                'key' => 'small_text',
                'label' => 'About - Teks Kecil',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'title',
                'label' => 'About - Judul',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'paragraph_1',
                'label' => 'About - Paragraf 1',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'section' => 'about',
                'key' => 'paragraph_2',
                'label' => 'About - Paragraf 2',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'section' => 'about',
                'key' => 'button_text',
                'label' => 'About - Teks Tombol',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'button_url',
                'label' => 'About - URL Tombol',
                'type' => 'url',
                'placeholder' => 'https://',
            ],
            [
                'section' => 'about',
                'key' => 'investment_value',
                'label' => 'About - Investment (angka)',
                'type' => 'text',
                'placeholder' => 'Contoh: 1B+',
            ],
            [
                'section' => 'about',
                'key' => 'investment_label',
                'label' => 'About - Investment (label)',
                'type' => 'text',
                'placeholder' => 'Contoh: Investment Funds',
            ],
            [
                'section' => 'about',
                'key' => 'co_image',
                'label' => 'About - Gambar CO',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'about',
                'key' => 'co_name',
                'label' => 'About - Nama CO',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'co_role',
                'label' => 'About - Role CO',
                'type' => 'text',
                'placeholder' => 'Contoh: CO',
            ],
            [
                'section' => 'about',
                'key' => 'ceo_image',
                'label' => 'About - Gambar CEO',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'about',
                'key' => 'ceo_name',
                'label' => 'About - Nama CEO',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'ceo_role',
                'label' => 'About - Role CEO',
                'type' => 'text',
                'placeholder' => 'Contoh: CEO',
            ],
            [
                'section' => 'about',
                'key' => 'attendees_value',
                'label' => 'About - Attendees (angka)',
                'type' => 'text',
                'placeholder' => 'Contoh: 2K+',
            ],
            [
                'section' => 'about',
                'key' => 'attendees_label',
                'label' => 'About - Attendees (label)',
                'type' => 'text',
                'placeholder' => 'Contoh: Attendees',
            ],
            [
                'section' => 'about',
                'key' => 'counter_1_value',
                'label' => 'About - Counter 1 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'about',
                'key' => 'counter_1_label',
                'label' => 'About - Counter 1 (label)',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'counter_2_value',
                'label' => 'About - Counter 2 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'about',
                'key' => 'counter_2_label',
                'label' => 'About - Counter 2 (label)',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'counter_3_value',
                'label' => 'About - Counter 3 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'about',
                'key' => 'counter_3_label',
                'label' => 'About - Counter 3 (label)',
                'type' => 'text',
            ],
            [
                'section' => 'about',
                'key' => 'counter_4_value',
                'label' => 'About - Counter 4 (angka)',
                'type' => 'number',
            ],
            [
                'section' => 'about',
                'key' => 'counter_4_suffix',
                'label' => 'About - Counter 4 (suffix)',
                'type' => 'text',
                'placeholder' => 'Contoh: X',
            ],
            [
                'section' => 'about',
                'key' => 'counter_4_label',
                'label' => 'About - Counter 4 (label)',
                'type' => 'text',
            ],

            [
                'section' => 'sponsors',
                'key' => 'logo_1',
                'label' => 'Sponsors - Logo 1',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'sponsors',
                'key' => 'logo_2',
                'label' => 'Sponsors - Logo 2',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'sponsors',
                'key' => 'logo_3',
                'label' => 'Sponsors - Logo 3',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'sponsors',
                'key' => 'logo_4',
                'label' => 'Sponsors - Logo 4',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],
            [
                'section' => 'sponsors',
                'key' => 'logo_5',
                'label' => 'Sponsors - Logo 5',
                'type' => 'image',
                'help' => 'JPEG/PNG, maks 2MB. Disimpan ke assetsAdmin/uploads/konten-halaman.',
            ],

            [
                'section' => 'registration',
                'key' => 'small_text',
                'label' => 'Registration - Teks Kecil',
                'type' => 'text',
            ],
            [
                'section' => 'registration',
                'key' => 'title',
                'label' => 'Registration - Judul',
                'type' => 'text',
            ],
            [
                'section' => 'registration',
                'key' => 'button_text',
                'label' => 'Registration - Teks Tombol',
                'type' => 'text',
            ],
            [
                'section' => 'registration',
                'key' => 'button_url',
                'label' => 'Registration - URL Tombol',
                'type' => 'url',
                'placeholder' => 'https://',
            ],
        ];
    }
}
