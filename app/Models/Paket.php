<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;

    protected $table = 'paket';

    protected $fillable = [
        'event_id',
        'nama_paket',
        'harga',
        'akses_live',
        'akses_rekaman',
        'kuota',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function sesi()
    {
        return $this->belongsToMany(
            EventSesi::class,
            'paket_sesi'
        );
    }
}
