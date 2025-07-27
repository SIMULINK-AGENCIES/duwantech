<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // E-commerce Event Listeners
        \App\Events\NewOrderEvent::class => [
            \App\Listeners\OrderNotificationListener::class,
        ],

        \App\Events\PaymentProcessedEvent::class => [
            \App\Listeners\PaymentNotificationListener::class,
        ],

        \App\Events\StockAlertEvent::class => [
            \App\Listeners\InventoryNotificationListener::class,
        ],

        \App\Events\NewActivityEvent::class => [
            \App\Listeners\UserActivityListener::class,
        ],

        \App\Events\UserOnlineEvent::class => [
            \App\Listeners\UserActivityListener::class,
        ],

        \App\Events\UserOfflineEvent::class => [
            \App\Listeners\UserActivityListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
