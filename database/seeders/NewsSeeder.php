<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status_akun' => 'aktif',
            ]
        );

        $categories = [
            ['nama' => 'Teknologi', 'slug' => 'teknologi'],
            ['nama' => 'Olahraga', 'slug' => 'olahraga'],
            ['nama' => 'Bisnis', 'slug' => 'bisnis'],
        ];

        $categoryMap = [];
        foreach ($categories as $c) {
            $model = NewsCategory::query()->updateOrCreate(
                ['slug' => $c['slug']],
                ['nama' => $c['nama'], 'slug' => $c['slug']]
            );
            $categoryMap[$c['slug']] = $model;
        }

        $items = [
            [
                'category_slug' => 'teknologi',
                'judul' => 'Laravel 12 Semakin Matang: Apa yang Perlu Disiapkan Tim Engineering?',
                'slug' => 'laravel-12-semakin-matang-apa-yang-perlu-disiapkan-tim-engineering',
                'status' => 'published',
                'published_at' => now()->subDays(6)->setTime(10, 15),
                'meta_keywords' => 'laravel,php,backend,engineering,release',
                'konten' => implode("\n\n", [
                    'Rilis versi mayor selalu menjadi momen yang menarik bagi tim engineering. Di satu sisi, ada peluang untuk merapikan fondasi aplikasi: mengurangi technical debt, memperbaiki struktur modul, serta menstandardisasi cara kerja. Di sisi lain, upgrade besar sering memunculkan kekhawatiran seputar kompatibilitas, perubahan perilaku, dan biaya migrasi.',
                    'Untuk memaksimalkan transisi, langkah pertama adalah memetakan dependensi. Buat daftar paket yang paling kritikal (auth, pembayaran, queue, storage, dan logging). Setelah itu, tentukan strategi upgrade: apakah langsung lompat versi di cabang terpisah, atau bertahap sambil memastikan test suite tetap hijau.',
                    'Hal kedua adalah menyepakati standar kualitas. Minimal, pastikan aplikasi punya pengujian untuk jalur paling penting: login, pembelian paket, dan akses konten. Dari situ, barulah tim bisa bergerak lebih cepat tanpa takut mengorbankan stabilitas.',
                    'Terakhir, jangan lupakan operasional. Rencanakan perubahan konfigurasi environment, pipeline CI/CD, dan observability. Upgrade paling sukses biasanya bukan yang tercepat, tetapi yang paling terukur: progres jelas, risiko dipetakan, dan tim punya waktu untuk memperbaiki masalah sebelum rilis ke produksi.',
                ]),
            ],
            [
                'category_slug' => 'bisnis',
                'judul' => 'Strategi Harga Paket Webinar: Early Bird, Reguler, atau VIP?',
                'slug' => 'strategi-harga-paket-webinar-early-bird-reguler-atau-vip',
                'status' => 'published',
                'published_at' => now()->subDays(3)->setTime(16, 40),
                'meta_keywords' => 'pricing,webinar,bisnis,marketing,conversion',
                'konten' => implode("\n\n", [
                    'Menentukan paket untuk sebuah webinar bukan sekadar memilih angka. Paket adalah cara Anda mengomunikasikan nilai, membangun urgensi, sekaligus mengelola kapasitas. Karena itu, tiga tipe paket paling umum—early bird, reguler, dan VIP—sebaiknya diposisikan dengan tujuan yang berbeda.',
                    'Early bird efektif untuk menggerakkan penjualan di awal. Tujuannya bukan margin maksimal, melainkan momentum: memastikan event terlihat hidup, mengurangi kekhawatiran calon peserta, dan memberi sinyal bahwa event akan ramai. Umumnya early bird punya kuota terbatas dan benefit yang sederhana.',
                    'Paket reguler adalah tulang punggung pendapatan. Pastikan benefitnya jelas: akses live semua sesi, materi, dan sertifikat. Harganya perlu terasa “wajar” dibandingkan outcome yang didapat peserta, terutama jika audiens Anda adalah profesional yang mempertimbangkan ROI.',
                    'VIP cocok untuk segmen yang mengutamakan pengalaman. Tambahkan benefit yang benar-benar terasa berbeda, misalnya akses rekaman, prioritas Q&A, atau sesi konsultasi terjadwal. Kunci sukses VIP adalah pembeda yang spesifik, bukan sekadar label. Jika VIP hanya lebih mahal tanpa nilai tambah yang nyata, konversi akan rendah.',
                ]),
            ],
            [
                'category_slug' => 'olahraga',
                'judul' => 'Menjaga Konsistensi Latihan di Tengah Jadwal Padat: Mulai dari 20 Menit',
                'slug' => 'menjaga-konsistensi-latihan-di-tengah-jadwal-padat-mulai-dari-20-menit',
                'status' => 'draft',
                'published_at' => null,
                'meta_keywords' => 'olahraga,kesehatan,rutinitas,latihan,kebugaran',
                'konten' => implode("\n\n", [
                    'Banyak orang gagal menjaga rutinitas latihan bukan karena tidak punya niat, tetapi karena rencana yang terlalu ideal. Target 60 menit setiap hari terdengar bagus, namun sulit dipertahankan ketika pekerjaan dan aktivitas lain menumpuk.',
                    'Pendekatan yang lebih realistis adalah memulai dari 20 menit. Fokus pada latihan sederhana yang bisa dilakukan tanpa banyak persiapan: pemanasan singkat, gerakan inti seperti squat atau push-up, dan pendinginan. Dengan durasi yang pendek, hambatan untuk mulai jauh lebih rendah.',
                    'Konsistensi adalah akumulasi keputusan kecil. Ketika rutinitas 20 menit sudah terbentuk, barulah Anda bisa menambah intensitas atau durasi secara bertahap. Yang terpenting, pilih waktu latihan yang paling mungkin Anda patuhi—pagi sebelum aktivitas dimulai, atau sore setelah pekerjaan selesai.',
                    'Artikel ini masih draft dan akan dilengkapi dengan contoh program mingguan, variasi gerakan, serta cara mengukur progres tanpa membuat Anda kelelahan.',
                ]),
            ],
        ];

        foreach ($items as $item) {
            $category = $categoryMap[$item['category_slug']] ?? null;
            if (! $category) {
                continue;
            }

            $metaDescription = Str::limit(strip_tags($item['konten']), 150, '');

            News::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'news_category_id' => $category->id,
                    'judul' => $item['judul'],
                    'slug' => $item['slug'],
                    'konten' => $item['konten'],
                    'gambar_utama' => null,
                    'status' => $item['status'],
                    'published_at' => $item['published_at'],
                    'meta_description' => $metaDescription,
                    'meta_keywords' => $item['meta_keywords'],
                    'created_by' => $admin->id,
                ]
            );
        }
    }
}
