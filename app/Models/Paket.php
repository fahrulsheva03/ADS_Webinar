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
        'deskripsi',
        'harga',
        'status',
        'akses_live',
        'akses_rekaman',
        'kuota',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'akses_live' => 'boolean',
        'akses_rekaman' => 'boolean',
        'kuota' => 'integer',
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
