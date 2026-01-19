<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@webinar.local'],
            [
                'nama' => 'Admin',
                'password' => Hash::make(Str::random(64)),
                'role' => 'admin',
                'status_akun' => 'aktif',
            ]
        );
    }
}
