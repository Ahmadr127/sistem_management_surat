<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use App\View\Components\AppLayout;

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
        // Daftarkan component AppLayout
        Blade::component('app-layout', AppLayout::class);

        // Force HTTPS jika di set di .env (untuk mengatasi warning form submission not secure di production)
        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
