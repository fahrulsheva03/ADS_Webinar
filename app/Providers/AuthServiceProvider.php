<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\EventSesi;
use App\Models\VideoSesi;
use App\Policies\EventPolicy;
use App\Policies\EventSesiPolicy;
use App\Policies\VideoSesiPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        EventSesi::class => EventSesiPolicy::class,
        VideoSesi::class => VideoSesiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
