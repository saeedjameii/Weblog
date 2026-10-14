<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
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

        foreach (Permission::query()->pluck('name') as $permissionName) {
            Gate::define($permissionName, function($user) use ($permissionName){
                return $user->hasPermission($permissionName);
            });
        }
    }
}
