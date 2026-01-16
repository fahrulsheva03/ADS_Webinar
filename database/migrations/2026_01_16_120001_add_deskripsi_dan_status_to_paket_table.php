<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('nama_paket');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'status']);
        });
    }
};
