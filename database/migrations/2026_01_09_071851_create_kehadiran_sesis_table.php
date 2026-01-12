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
        Schema::create('kehadiran_sesi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('event_sesi_id')
                ->constrained('event_sesi')
                ->cascadeOnDelete();

            $table->dateTime('waktu_join');
            $table->dateTime('waktu_leave')->nullable();
            $table->integer('durasi_menit')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'event_sesi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_sesis');
    }
};
