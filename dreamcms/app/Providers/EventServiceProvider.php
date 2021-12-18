<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Steam\SteamExtendSocialite;
use SocialiteProviders\VKontakte\VKontakteExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\VKontakte\\VKontakteExtendSocialite@handle',
            'SocialiteProviders\\Discord\\DiscordExtendSocialite@handle',
            'SocialiteProviders\\Steam\\SteamExtendSocialite@handle',
            'SocialiteProviders\\Telegram\\TelegramExtendSocialite@handle',
            'SocialiteProviders\\Google\\GoogleExtendSocialite@handle',
            'SocialiteProviders\\Yandex\\YandexExtendSocialite@handle',
            'SocialiteProviders\\Mailru\\MailruExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
