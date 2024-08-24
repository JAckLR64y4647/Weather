<?php

namespace App\Providers;

use App\Models\User;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            return env('APP_URL') . '/verify-email?' . http_build_query([
                    'link' => URL::temporarySignedRoute(
                        'verification.verify',
                        now()->addMinutes(Config::get('auth.verification.expire', 60)),
                        [
                            'id' => $notifiable->getKey(),
                            'hash' => sha1($notifiable->getEmailForVerification()),
                        ],
                    ),
                ]);
        });

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('APP_URL') . '/reset-password?' . http_build_query([
                    'link' => env('APP_URL') . '/api/v1/auth/forgot-password/verify?token=' . $token . '&email=' . $user->email,
                ]);
        });
    }
}
