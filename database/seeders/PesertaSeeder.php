<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PesertaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('id_ID');

        $seedUsers = [
            [
                'nama' => 'fahrul',
                'email' => 'fahrulshevavanjovie@gmail.com',
                'password_plain' => 'fahrul123',
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'Nadia Putri Ramadhani',
                'email' => 'nadia.ramadhani@example.com',
                'password_plain' => 'Nadia#2026!',
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'Rizky Fadhillah Pratama',
                'email' => 'rizky.pratama@example.com',
                'password_plain' => 'RizkySecure2026',
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'Siti Aisyah Nuraini',
                'email' => 'aisyah.nuraini@example.com',
                'password_plain' => 'Aisyah_12345',
                'status_akun' => 'nonaktif',
            ],
        ];

        foreach ($seedUsers as $row) {
            $email = strtolower((string) $row['email']);

            $existing = User::query()->where('email', $email)->first();

            if ($existing) {
                $existing->update([
                    'nama' => $row['nama'],
                    'password' => Hash::make((string) $row['password_plain']),
                    'role' => 'user',
                    'status_akun' => $row['status_akun'],
                ]);

                continue;
            }

            $registeredAt = $faker->dateTimeBetween('-6 months', 'now');

            User::query()->create([
                'nama' => $row['nama'],
                'email' => $email,
                'password' => Hash::make((string) $row['password_plain']),
                'role' => 'user',
                'status_akun' => $row['status_akun'],
                'created_at' => $registeredAt,
                'updated_at' => $registeredAt,
            ]);
        }
    }
}
