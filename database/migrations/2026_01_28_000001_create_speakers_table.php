<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('jabatan', 150);
            $table->string('perusahaan', 150);
            $table->string('linkedin_url', 2048)->nullable();
            $table->string('foto', 2048)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'urutan']);
            $table->index(['nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speakers');
    }
};
