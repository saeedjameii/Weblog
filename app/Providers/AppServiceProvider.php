<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Cache;
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

        // $permissions = Cache::remember('permissions.all', 3600, function () {
        //     return Permission::all();
        // });
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            Gate::define($permission->name, function($user) use ($permission){
                return $user->hasPermission($permission->name);
            });
        }
    }
}
