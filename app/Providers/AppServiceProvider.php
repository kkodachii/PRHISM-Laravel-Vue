<?php

namespace App\Providers;

use Inertia\Inertia;
use App\Models\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env(key: 'APP_ENV') === 'local' && request()->server(key: 'HTTP_X_FORWARDED_PROTO') === 'https') {
    URL::forceScheme(scheme: 'https');
}
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Register middleware
        Route::aliasMiddleware('roles', RoleMiddleware::class);

        Inertia::share([
            'notifications' => function () {
                return Auth::user() ? Auth::user()->unreadNotifications : [];
            },
            'users' => function () {
                // Fetch all users or filter based on your requirements
                return User::all(['id', 'name', 'profile_picture'])->toArray();
            },
        ]);


    VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
        return (new MailMessage)
->view('emails.verification',[
'user' => $notifiable,
'url' => $url,

]);
    });

    ResetPassword::toMailUsing(function ($notifiable, $token) {
        $url = URL::temporarySignedRoute(
            'password.reset', // Adjust this route name if necessary
            now()->addMinutes(60),
            ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]
        );

        return (new MailMessage)
            ->subject('Reset Your Password') // Customize the subject
            ->view('emails.password_reset', [ // Use your custom view here
                'user' => $notifiable,
                'url' => $url,
            ]);
    });


    }
}
