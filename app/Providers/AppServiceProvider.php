<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Application service provider for shared bootstrapping concerns.
 */
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
        // Share authenticated user data with views in the same array shape expected by current Blade templates.
        View::composer('*', function ($view): void {
            if (! Schema::hasTable('users')) {
                $view->with('currentOjtUser', null);

                return;
            }

            $user = User::query()->find(Auth::id());
            $userArray = $user?->toArray();

            $view->with('currentOjtUser', $userArray);
        });
    }
}
