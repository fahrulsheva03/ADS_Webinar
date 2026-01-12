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
        Schema::create('video_sesi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_sesi_id')
                ->constrained('event_sesi')
                ->cascadeOnDelete();

            $table->string('judul_video');
            $table->string('url_video');
            $table->integer('durasi_menit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_sesis');
    }
};
