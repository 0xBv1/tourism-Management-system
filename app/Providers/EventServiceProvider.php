<?php

namespace App\Providers;

use App\Events\ChatMessageSent;
use App\Events\InquiryConfirmed;
use App\Events\NewInquiryCreated;
use App\Events\PaymentReceived;
use App\Listeners\GenerateBookingFileListener;
use App\Listeners\SendChatMessageNotification;
use App\Listeners\SendNewInquiryNotification;
use App\Listeners\UpdateBookingFileStatusListener;
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
        InquiryConfirmed::class => [
            GenerateBookingFileListener::class,
        ],
        PaymentReceived::class => [
            UpdateBookingFileStatusListener::class,
        ],
        ChatMessageSent::class => [
            SendChatMessageNotification::class,
        ],
        NewInquiryCreated::class => [
            SendNewInquiryNotification::class,
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

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}