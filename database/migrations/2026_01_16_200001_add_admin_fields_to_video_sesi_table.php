<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_sesi', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('url_video');
            $table->string('file_name')->nullable()->after('file_path');
            $table->unsignedBigInteger('file_size_bytes')->nullable()->after('file_name');
            $table->string('mime_type')->nullable()->after('file_size_bytes');
            $table->string('thumbnail_path')->nullable()->after('mime_type');
            $table->json('tags')->nullable()->after('thumbnail_path');
            $table->enum('status', ['published', 'draft'])->default('published')->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('video_sesi', function (Blueprint $table) {
            $table->dropColumn([
                'file_path',
                'file_name',
                'file_size_bytes',
                'mime_type',
                'thumbnail_path',
                'tags',
                'status',
            ]);
        });
    }
};
