<?php

namespace App\Providers;

use App\Services\OjtUserStorage;
use Illuminate\Support\Facades\View;
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
        // Share current OJT user (from session + JSON storage) so views can show name, email, etc.
        View::composer('*', function ($view): void {
            $userId = session('ojt_user_id');
            $view->with('currentOjtUser', $userId
                ? app(OjtUserStorage::class)->findById($userId)
                : null);
        });
    }
}
