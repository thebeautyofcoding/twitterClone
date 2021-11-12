<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
        'App\Events\LikeEvent' => ['App\Listeners\LikeEventListener'],
        'App\Events\RetweetEvent' => ['App\Listeners\RetweetEventListener'],
        'App\Events\UnlikeEvent' => ['App\Listeners\UnlikeEventListener'],
        'App\Events\MessageSentEvent' => [
            'App\Listeners\MessageSentEventListener',
        ],
        'App\Events\JoinedRoomEvent' => [
            'App\Listeners\JoinedRoomEventListener',
        ],
        'App\Events\PostPostedEvent' => [
            'App\Listeners\PostPostedEventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
