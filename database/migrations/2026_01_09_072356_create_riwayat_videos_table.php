<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('video_sesi_id')
                ->constrained('video_sesi')
                ->cascadeOnDelete();

            $table->dateTime('terakhir_ditonton')->nullable();
            $table->integer('durasi_ditonton')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'video_sesi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_videos');
    }
};
