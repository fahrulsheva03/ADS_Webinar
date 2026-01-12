<?php

namespace App\Policies;

use App\Models\EventSesi;
use App\Models\Pesanan;
use App\Models\User;

class EventSesiPolicy
{
    public function joinZoom(User $user, EventSesi $sesi): bool
    {
        // 1. Cari pesanan PAID user untuk event ini
        $pesanan = Pesanan::where('user_id', $user->id)
            ->where('status_pembayaran', 'paid')
            ->whereHas('paket', function ($q) use ($sesi) {
                $q->where('event_id', $sesi->event_id)
                    ->where('akses_live', true);
            })
            ->exists();

        if (! $pesanan) {
            return false;
        }

        // 2. Validasi waktu
        $now = now();
        if ($now->lt($sesi->waktu_mulai) || $now->gt($sesi->waktu_selesai)) {
            return false;
        }

        // 3. Validasi status sesi
        if ($sesi->status_sesi !== 'live') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EventSesi $eventSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EventSesi $eventSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventSesi $eventSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventSesi $eventSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventSesi $eventSesi): bool
    {
        return false;
    }
}
