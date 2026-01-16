<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoSesi extends Model
{
    use HasFactory;

    protected $table = 'video_sesi';

    protected $fillable = [
        'event_sesi_id',
        'judul_video',
        'url_video',
        'file_path',
        'file_name',
        'file_size_bytes',
        'mime_type',
        'thumbnail_path',
        'tags',
        'status',
        'durasi_menit',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'tags' => 'array',
        'durasi_menit' => 'integer',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function sesi()
    {
        return $this->belongsTo(EventSesi::class, 'event_sesi_id');
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatVideo::class);
    }

    public function generateThumbnailIfMissing(): bool
    {
        if (! empty($this->thumbnail_path)) {
            return true;
        }

        if (empty($this->file_path)) {
            return false;
        }

        $localVideoPath = storage_path('app/public/'.ltrim($this->file_path, '/\\'));
        if (! is_file($localVideoPath)) {
            return false;
        }

        $ffmpeg = (string) env('FFMPEG_PATH', 'ffmpeg');
        if ($ffmpeg === '') {
            return false;
        }

        $tmpBase = tempnam(sys_get_temp_dir(), 'vidthumb_');
        if ($tmpBase === false) {
            return false;
        }

        $tmpJpg = $tmpBase.'.jpg';
        @unlink($tmpBase);

        try {
            $attempts = ['00:00:01', '00:00:00'];
            foreach ($attempts as $seek) {
                $result = Process::run([
                    $ffmpeg,
                    '-y',
                    '-hide_banner',
                    '-loglevel',
                    'error',
                    '-ss',
                    $seek,
                    '-i',
                    $localVideoPath,
                    '-frames:v',
                    '1',
                    '-vf',
                    'scale=640:-1',
                    '-q:v',
                    '3',
                    $tmpJpg,
                ]);

                if ($result->successful() && is_file($tmpJpg) && filesize($tmpJpg) > 0) {
                    break;
                }
            }

            if (! is_file($tmpJpg) || filesize($tmpJpg) <= 0) {
                return false;
            }

            $dest = 'videos/thumbnails/'.$this->id.'-'.Str::random(10).'.jpg';
            $ok = Storage::disk('public')->put($dest, file_get_contents($tmpJpg));
            if (! $ok) {
                return false;
            }

            $this->forceFill(['thumbnail_path' => $dest])->save();

            return true;
        } catch (\Throwable) {
            return false;
        } finally {
            @unlink($tmpJpg);
        }
    }
}
