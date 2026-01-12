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
        Schema::create('event_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->string('judul_sesi');
            $table->text('deskripsi_sesi')->nullable();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');

            $table->string('zoom_link')->nullable();
            $table->enum('status_sesi', ['upcoming', 'live', 'selesai'])->default('upcoming');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sesis');
    }
};
