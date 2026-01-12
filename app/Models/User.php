<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status_akun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function kehadiranSesi()
    {
        return $this->hasMany(KehadiranSesi::class);
    }

    public function riwayatVideo()
    {
        return $this->hasMany(RiwayatVideo::class);
    }
}
