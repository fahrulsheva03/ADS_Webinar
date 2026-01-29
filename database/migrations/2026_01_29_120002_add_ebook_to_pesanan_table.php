<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pesanan')) {
            return;
        }

        if (Schema::hasColumn('pesanan', 'ebook_id')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::create('pesanan_tmp', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('paket_id')
                ->nullable()
                ->constrained('paket')
                ->restrictOnDelete();

            $table->foreignId('ebook_id')
                ->nullable()
                ->constrained('ebooks')
                ->restrictOnDelete();

            $table->string('kode_pesanan')->unique();
            $table->enum('status_pembayaran', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->decimal('total_bayar', 12, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->dateTime('waktu_bayar')->nullable();

            $table->timestamps();
        });

        DB::table('pesanan_tmp')->insertUsing(
            [
                'id',
                'user_id',
                'paket_id',
                'ebook_id',
                'kode_pesanan',
                'status_pembayaran',
                'total_bayar',
                'metode_pembayaran',
                'waktu_bayar',
                'created_at',
                'updated_at',
            ],
            DB::table('pesanan')->selectRaw(
                'id, user_id, paket_id, NULL as ebook_id, kode_pesanan, status_pembayaran, total_bayar, metode_pembayaran, waktu_bayar, created_at, updated_at'
            )
        );

        Schema::drop('pesanan');
        Schema::rename('pesanan_tmp', 'pesanan');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        if (! Schema::hasTable('pesanan')) {
            return;
        }

        if (! Schema::hasColumn('pesanan', 'ebook_id')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::create('pesanan_tmp', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('paket_id')
                ->constrained('paket')
                ->restrictOnDelete();

            $table->string('kode_pesanan')->unique();
            $table->enum('status_pembayaran', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->decimal('total_bayar', 12, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->dateTime('waktu_bayar')->nullable();

            $table->timestamps();
        });

        DB::table('pesanan_tmp')->insertUsing(
            [
                'id',
                'user_id',
                'paket_id',
                'kode_pesanan',
                'status_pembayaran',
                'total_bayar',
                'metode_pembayaran',
                'waktu_bayar',
                'created_at',
                'updated_at',
            ],
            DB::table('pesanan')->whereNotNull('paket_id')->select(
                [
                    'id',
                    'user_id',
                    'paket_id',
                    'kode_pesanan',
                    'status_pembayaran',
                    'total_bayar',
                    'metode_pembayaran',
                    'waktu_bayar',
                    'created_at',
                    'updated_at',
                ]
            )
        );

        Schema::drop('pesanan');
        Schema::rename('pesanan_tmp', 'pesanan');

        Schema::enableForeignKeyConstraints();
    }
};
