<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::define('permission', function($user, $permission){
            return $user->hasPermission($permission);
        });

        Gate::define('access-panel', function($user){
            return $user->canAccessPanel();
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('تأیید نشانی ایمیل')
                ->view(
                    ['emails.auth.verify-email', 'emails.auth.verify-email-text'],
                    ['url' => $url, 'user' => $notifiable]
                );
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token){
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)->subject('بازیابی رمز عبور — چی بهتره؟')->view('emails.auth.reset-password', [
                'user' => $notifiable,
                'url' => $url,
            ]);
        });

        if (! Schema::hasTable('permissions')) {
            return;
        }

        foreach (Permission::query()->pluck('name') as $permissionName) {
            Gate::define($permissionName, function($user) use ($permissionName){
                return $user->hasPermission($permissionName);
            });
        }
    }
}
