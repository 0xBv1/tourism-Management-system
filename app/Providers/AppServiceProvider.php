<?php

namespace App\Providers;

use App\Enums\SettingKey;
use App\Enums\PaymentMethod;
use App\Payments\PaymentFactory;
use App\Payments\PaymentGateway;
use App\Channels\WhatsappChannel;
use App\Notifications\Channels\WhatsAppChannel as WhatsAppNotificationChannel;
use App\Notifications\Channels\SmsChannel;
use App\Services\Recaptcha\RecaptchaService;
use App\Models\BookingFile;
use App\Observers\BookingFileObserver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Services\Translation\Google\FreeTranslator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $app = $this->app;
        $this->app->make(ChannelManager::class)->extend('whatsapp', function () use ($app) {
            return $app->make(WhatsappChannel::class);
        });

        $this->app->make(ChannelManager::class)->extend('whatsapp_notification', function () use ($app) {
            return $app->make(WhatsAppNotificationChannel::class);
        });

        $this->app->make(ChannelManager::class)->extend('sms', function () use ($app) {
            return $app->make(SmsChannel::class);
        });

        $this->app->bind(PaymentGateway::class,
            fn() => PaymentFactory::instance(request('payment_method', PaymentMethod::COD->value)));

            $this->app->bind(RecaptchaService::class, function ($app) {
                return new RecaptchaService();
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        File::macro('isEmptyDir', function ($path) {
            return count(glob("$path/*")) === 0;
        });
        
        // Register observers
        BookingFile::observe(BookingFileObserver::class);
        
//        Str::macro('htmlEntityDecode', fn($value) => Str::of(html_entity_decode($value)));
//        Stringable::macro('htmlEntityDecode', fn() => new Stringable(html_entity_decode($this->value)));
    }
}
