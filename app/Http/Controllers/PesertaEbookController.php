<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PesertaEbookController extends Controller
{
    public function download(Ebook $ebook)
    {
        abort_if(! Schema::hasTable('ebooks'), 404);
        abort_if(! $ebook->is_active, 404);

        $raw = trim((string) ($ebook->pdf_file ?? ''));
        abort_if($raw === '', 404);

        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        abort_if(str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        abort_if(! $disk->exists($path), 404);

        $filename = trim((string) ($ebook->title ?? 'ebook'));
        $filename = $filename !== '' ? $filename : 'ebook';
        $filename = preg_replace('/[^A-Za-z0-9 _.-]/', '', $filename) ?: 'ebook';

        return $disk->download($path, $filename.'.pdf');
    }
}
