<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Speaker extends Model
{
    use HasFactory;

    protected $table = 'speakers';

    protected $fillable = [
        'nama',
        'jabatan',
        'perusahaan',
        'linkedin_url',
        'foto',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        $raw = trim((string) ($this->foto ?? ''));

        if ($raw === '') {
            return null;
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        $path = ltrim($raw, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (Storage::disk('public')->exists($path)) {
            return route('speakers.image', $this);
        }

        return asset($raw);
    }
}
