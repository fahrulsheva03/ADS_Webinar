<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSesi extends Model
{
    use HasFactory;

    protected $table = 'event_sesi';

    protected $fillable = [
        'event_id',
        'judul_sesi',
        'deskripsi_sesi',
        'waktu_mulai',
        'waktu_selesai',
        'zoom_link',
        'status_sesi',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function kehadiran()
    {
        return $this->hasMany(KehadiranSesi::class);
    }

    public function video()
    {
        return $this->hasMany(VideoSesi::class);
    }

    public function paket()
    {
        return $this->belongsToMany(
            Paket::class,
            'paket_sesi'
        );
    }
}
