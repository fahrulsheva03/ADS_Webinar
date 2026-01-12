<?php

namespace App\Policies;

use App\Models\Pesanan;
use App\Models\User;
use App\Models\VideoSesi;

class VideoSesiPolicy
{
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
    public function view(User $user, VideoSesi $video): bool
    {
        $sesi = $video->sesi;

        return Pesanan::where('user_id', $user->id)
            ->where('status_pembayaran', 'paid')
            ->whereHas('paket', function ($q) use ($sesi) {
                $q->where('event_id', $sesi->event_id)
                    ->where('akses_rekaman', true);
            })
            ->exists()
            && $sesi->event->status === 'finished';
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
    public function update(User $user, VideoSesi $videoSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VideoSesi $videoSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VideoSesi $videoSesi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VideoSesi $videoSesi): bool
    {
        return false;
    }
}
