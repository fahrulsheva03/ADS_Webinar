<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSesi;
use App\Models\Paket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EventPaketSeeder extends Seeder
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

        $now = now();

        $events = [
            [
                'judul' => 'Webinar Laravel 12: From Zero to Deploy',
                'deskripsi' => 'Webinar intensif 2 hari untuk membahas fundamental Laravel 12, arsitektur project yang rapi, best practice keamanan, hingga strategi deployment yang stabil. Cocok untuk developer pemula sampai intermediate yang ingin siap produksi.',
                'lokasi' => 'Online (Zoom)',
                'tanggal_mulai' => $now->copy()->addDays(14)->toDateString(),
                'tanggal_selesai' => $now->copy()->addDays(15)->toDateString(),
                'status' => 'active',
                'sesi' => [
                    [
                        'judul_sesi' => 'Keynote: Roadmap Laravel 12 & Praktik Modern PHP',
                        'deskripsi_sesi' => 'Pembukaan event, roadmap fitur, dan pola kerja modern di Laravel. Pembicara: Tim Engineering.',
                        'start' => ['day_offset' => 0, 'time' => '09:00'],
                        'durasi_menit' => 90,
                        'status_sesi' => 'upcoming',
                    ],
                    [
                        'judul_sesi' => 'Hands-on: Auth, Policies, dan Struktur Modul',
                        'deskripsi_sesi' => 'Praktik membangun autentikasi, policy/gate, serta struktur modul yang maintainable.',
                        'start' => ['day_offset' => 0, 'time' => '13:00'],
                        'durasi_menit' => 90,
                        'status_sesi' => 'upcoming',
                    ],
                    [
                        'judul_sesi' => 'Deploy: CI/CD, Storage, dan Observability',
                        'deskripsi_sesi' => 'Strategi deployment, pengelolaan file, konfigurasi environment, dan monitoring sederhana.',
                        'start' => ['day_offset' => 1, 'time' => '09:30'],
                        'durasi_menit' => 90,
                        'status_sesi' => 'upcoming',
                    ],
                ],
                'paket' => [
                    [
                        'nama_paket' => 'Early Bird',
                        'deskripsi' => 'Harga promo untuk 100 pendaftar pertama. Akses live untuk sesi hari pertama.',
                        'harga' => 99000,
                        'status' => 'aktif',
                        'akses_live' => true,
                        'akses_rekaman' => false,
                        'kuota' => 100,
                        'assign' => 'day_0',
                    ],
                    [
                        'nama_paket' => 'Reguler',
                        'deskripsi' => 'Akses live seluruh sesi. Termasuk materi dan sertifikat digital.',
                        'harga' => 149000,
                        'status' => 'aktif',
                        'akses_live' => true,
                        'akses_rekaman' => false,
                        'kuota' => 500,
                        'assign' => 'all',
                    ],
                    [
                        'nama_paket' => 'VIP',
                        'deskripsi' => 'Akses live seluruh sesi + akses rekaman + prioritas Q&A.',
                        'harga' => 299000,
                        'status' => 'aktif',
                        'akses_live' => true,
                        'akses_rekaman' => true,
                        'kuota' => 80,
                        'assign' => 'all',
                    ],
                ],
            ],
            [
                'judul' => 'Data & AI for Business Bootcamp',
                'deskripsi' => 'Bootcamp 3 hari untuk membangun pemahaman data, metrik bisnis, dan pengenalan implementasi AI di workflow perusahaan. Fokus pada studi kasus, dashboard, dan pengambilan keputusan berbasis data.',
                'lokasi' => 'Bandung (Hybrid)',
                'tanggal_mulai' => $now->copy()->addDays(35)->toDateString(),
                'tanggal_selesai' => $now->copy()->addDays(37)->toDateString(),
                'status' => 'draft',
                'sesi' => [
                    [
                        'judul_sesi' => 'Business Metrics: KPI, Funnel, dan Retention',
                        'deskripsi_sesi' => 'Dasar metrik, perancangan KPI, dan cara membaca funnel untuk optimasi pertumbuhan.',
                        'start' => ['day_offset' => 0, 'time' => '10:00'],
                        'durasi_menit' => 120,
                        'status_sesi' => 'upcoming',
                    ],
                    [
                        'judul_sesi' => 'Dashboarding: Dari Data Mentah ke Insight',
                        'deskripsi_sesi' => 'Praktik menyusun dashboard yang actionable dan mudah dipahami stakeholder.',
                        'start' => ['day_offset' => 0, 'time' => '14:00'],
                        'durasi_menit' => 120,
                        'status_sesi' => 'upcoming',
                    ],
                    [
                        'judul_sesi' => 'Intro to AI: Use Case yang Realistis di Perusahaan',
                        'deskripsi_sesi' => 'Pengenalan konsep AI, data readiness, dan contoh penerapan untuk customer support & marketing.',
                        'start' => ['day_offset' => 1, 'time' => '10:00'],
                        'durasi_menit' => 120,
                        'status_sesi' => 'upcoming',
                    ],
                    [
                        'judul_sesi' => 'Workshop: Rencana Implementasi 30 Hari',
                        'deskripsi_sesi' => 'Menyusun rencana implementasi, backlog, dan risk mitigation untuk 30 hari pertama.',
                        'start' => ['day_offset' => 2, 'time' => '10:30'],
                        'durasi_menit' => 120,
                        'status_sesi' => 'upcoming',
                    ],
                ],
                'paket' => [
                    [
                        'nama_paket' => 'Reguler',
                        'deskripsi' => 'Akses live semua sesi. Termasuk worksheet dan template dashboard.',
                        'harga' => 399000,
                        'status' => 'aktif',
                        'akses_live' => true,
                        'akses_rekaman' => false,
                        'kuota' => 200,
                        'assign' => 'all',
                    ],
                    [
                        'nama_paket' => 'VIP',
                        'deskripsi' => 'Akses live + rekaman + sesi konsultasi kelompok (terjadwal).',
                        'harga' => 699000,
                        'status' => 'aktif',
                        'akses_live' => true,
                        'akses_rekaman' => true,
                        'kuota' => 50,
                        'assign' => 'all',
                    ],
                    [
                        'nama_paket' => 'Student',
                        'deskripsi' => 'Harga khusus pelajar/mahasiswa. Akses live untuk 2 sesi pertama.',
                        'harga' => 249000,
                        'status' => 'aktif',
                        'akses_live' => true,
                        'akses_rekaman' => false,
                        'kuota' => 75,
                        'assign' => 'first_two',
                    ],
                ],
            ],
            [
                'judul' => 'Webinar Security Basics untuk Aplikasi Web',
                'deskripsi' => 'Event ringkas untuk memperkuat fondasi keamanan: validasi input, sanitasi, proteksi CSRF, dan pola akses berbasis role. Studi kasus difokuskan pada aplikasi web yang umum di perusahaan.',
                'lokasi' => 'Online (Zoom)',
                'tanggal_mulai' => $now->copy()->subDays(20)->toDateString(),
                'tanggal_selesai' => $now->copy()->subDays(19)->toDateString(),
                'status' => 'finished',
                'sesi' => [
                    [
                        'judul_sesi' => 'Threat Modeling Sederhana untuk Tim Kecil',
                        'deskripsi_sesi' => 'Mengenali aset penting, permukaan serangan, dan prioritas mitigasi.',
                        'start' => ['day_offset' => 0, 'time' => '09:00'],
                        'durasi_menit' => 90,
                        'status_sesi' => 'selesai',
                    ],
                    [
                        'judul_sesi' => 'Input Validation & Output Escaping yang Konsisten',
                        'deskripsi_sesi' => 'Praktik validasi, sanitasi, dan escaping output untuk mencegah XSS dan injection.',
                        'start' => ['day_offset' => 0, 'time' => '13:30'],
                        'durasi_menit' => 90,
                        'status_sesi' => 'selesai',
                    ],
                    [
                        'judul_sesi' => 'Access Control: Role, Policy, dan Audit',
                        'deskripsi_sesi' => 'Membangun kontrol akses yang rapi, plus checklist audit sederhana.',
                        'start' => ['day_offset' => 1, 'time' => '10:00'],
                        'durasi_menit' => 90,
                        'status_sesi' => 'selesai',
                    ],
                ],
                'paket' => [
                    [
                        'nama_paket' => 'Rekaman On-demand',
                        'deskripsi' => 'Akses rekaman seluruh sesi. Cocok untuk belajar mandiri setelah event selesai.',
                        'harga' => 129000,
                        'status' => 'aktif',
                        'akses_live' => false,
                        'akses_rekaman' => true,
                        'kuota' => null,
                        'assign' => 'all',
                    ],
                    [
                        'nama_paket' => 'Bundle Rekaman + Materi',
                        'deskripsi' => 'Akses rekaman seluruh sesi + materi PDF dan checklist audit keamanan.',
                        'harga' => 179000,
                        'status' => 'aktif',
                        'akses_live' => false,
                        'akses_rekaman' => true,
                        'kuota' => null,
                        'assign' => 'all',
                    ],
                    [
                        'nama_paket' => 'VIP Rekaman',
                        'deskripsi' => 'Akses rekaman + sesi tanya jawab tertulis via email selama 7 hari.',
                        'harga' => 249000,
                        'status' => 'aktif',
                        'akses_live' => false,
                        'akses_rekaman' => true,
                        'kuota' => 30,
                        'assign' => 'all',
                    ],
                ],
            ],
        ];

        foreach ($events as $payload) {
            DB::transaction(function () use ($payload, $admin, $now) {
                $existing = Event::query()->where('judul', $payload['judul'])->first();

                if ($existing) {
                    $existing->delete();
                }

                $event = Event::query()->create([
                    'judul' => $payload['judul'],
                    'deskripsi' => $payload['deskripsi'],
                    'tanggal_mulai' => $payload['tanggal_mulai'],
                    'tanggal_selesai' => $payload['tanggal_selesai'],
                    'lokasi' => $payload['lokasi'],
                    'gambar_utama' => null,
                    'status' => $payload['status'],
                    'created_by' => $admin->id,
                ]);

                $startDate = Carbon::parse($payload['tanggal_mulai'])->startOfDay();

                $sesiModels = [];
                foreach ($payload['sesi'] as $idx => $s) {
                    $dayOffset = (int) $s['start']['day_offset'];
                    $time = (string) $s['start']['time'];

                    $start = $startDate->copy()->addDays($dayOffset)->setTimeFromTimeString($time);
                    $end = $start->copy()->addMinutes((int) $s['durasi_menit']);

                    $zoomId = (string) (10000000000 + ($event->id * 100) + $idx);

                    $sesiModels[] = EventSesi::query()->create([
                        'event_id' => $event->id,
                        'judul_sesi' => $s['judul_sesi'],
                        'deskripsi_sesi' => $s['deskripsi_sesi'],
                        'waktu_mulai' => $start,
                        'waktu_selesai' => $end,
                        'zoom_link' => 'https://zoom.us/j/'.$zoomId,
                        'status_sesi' => $s['status_sesi'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $allSesiIds = collect($sesiModels)->pluck('id')->all();
                $day0SesiIds = collect($sesiModels)
                    ->filter(function (EventSesi $s) use ($payload) {
                        $start = Carbon::parse($payload['tanggal_mulai'])->startOfDay();

                        return Carbon::parse($s->waktu_mulai)->isSameDay($start);
                    })
                    ->pluck('id')
                    ->all();

                $firstTwoSesiIds = array_slice($allSesiIds, 0, 2);

                foreach ($payload['paket'] as $p) {
                    $paket = Paket::query()->create([
                        'event_id' => $event->id,
                        'nama_paket' => $p['nama_paket'],
                        'deskripsi' => $p['deskripsi'],
                        'harga' => $p['harga'],
                        'status' => $p['status'],
                        'akses_live' => (bool) $p['akses_live'],
                        'akses_rekaman' => (bool) $p['akses_rekaman'],
                        'kuota' => $p['kuota'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $assign = (string) $p['assign'];
                    $targetSesiIds = match ($assign) {
                        'day_0' => $day0SesiIds,
                        'first_two' => $firstTwoSesiIds,
                        default => $allSesiIds,
                    };

                    $paket->sesi()->sync($targetSesiIds);
                }
            });
        }
    }
}
