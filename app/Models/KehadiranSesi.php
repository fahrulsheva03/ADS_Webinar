<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranSesi extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_sesi';

    protected $fillable = [
        'user_id',
        'event_sesi_id',
        'waktu_join',
        'waktu_leave',
        'durasi_menit',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sesi()
    {
        return $this->belongsTo(EventSesi::class, 'event_sesi_id');
    }
}
