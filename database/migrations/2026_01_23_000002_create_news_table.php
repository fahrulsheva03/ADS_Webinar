<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            $table->foreignId('news_category_id')
                ->constrained('news_categories')
                ->restrictOnDelete();

            $table->string('judul');
            $table->string('slug')->unique();
            $table->longText('konten');
            $table->string('gambar_utama')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['news_category_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
