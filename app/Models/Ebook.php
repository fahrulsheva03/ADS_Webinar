<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ebook extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ebooks';

    protected $fillable = [
        'title',
        'author',
        'description',
        'cover_image',
        'pdf_file',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getCoverImageUrlAttribute(): ?string
    {
        $raw = trim((string) ($this->cover_image ?? ''));
        if ($raw === '') {
            return null;
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (str_starts_with($path, 'ebooks/')) {
            return route('admin.ebooks.cover', $this);
        }

        return asset($raw);
    }

    public function getPdfFileUrlAttribute(): ?string
    {
        $raw = trim((string) ($this->pdf_file ?? ''));
        if ($raw === '') {
            return null;
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (str_starts_with($path, 'ebooks/')) {
            return route('admin.ebooks.pdf', $this);
        }

        return asset($raw);
    }
}
