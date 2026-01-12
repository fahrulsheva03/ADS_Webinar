<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'paket_id',
        'kode_pesanan',
        'status_pembayaran',
        'total_bayar',
        'metode_pembayaran',
        'waktu_bayar',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
}
