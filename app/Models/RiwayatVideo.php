<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatVideo extends Model
{
    use HasFactory;

    protected $table = 'riwayat_video';

    protected $fillable = [
        'user_id',
        'video_sesi_id',
        'terakhir_ditonton',
        'durasi_ditonton',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(VideoSesi::class, 'video_sesi_id');
    }
}
