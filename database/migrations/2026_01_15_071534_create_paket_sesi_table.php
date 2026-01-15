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
        Schema::create('paket_sesi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paket_id')
                ->constrained('paket')
                ->cascadeOnDelete();

            $table->foreignId('event_sesi_id')
                ->constrained('event_sesi')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['paket_id', 'event_sesi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_sesi');
    }
};
