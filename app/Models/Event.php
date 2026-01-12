<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'created_by',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function sesi()
    {
        return $this->hasMany(EventSesi::class);
    }

    public function paket()
    {
        return $this->hasMany(Paket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
