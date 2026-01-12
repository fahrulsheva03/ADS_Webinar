<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoSesi extends Model
{
    use HasFactory;

    protected $table = 'video_sesi';

    protected $fillable = [
        'event_sesi_id',
        'judul_video',
        'url_video',
        'durasi_menit',
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
}
