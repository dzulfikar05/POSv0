<?php

namespace App\Providers;

use App\Models\SettingModel;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        date_default_timezone_set('Asia/Jakarta');

        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        if (Schema::hasTable('setting')) {
            $settings = SettingModel::whereIn('label', ['Nama Sistem', 'Alamat Toko', 'Whatsapp'])->pluck('value', 'label');

            View::share('app_name', $settings['Nama Sistem'] ?? 'POS');
            View::share('addressSystem', $settings['Alamat Toko'] ?? 'Malang');
            View::share('waSystem', $settings['Whatsapp'] ?? '-');
        }
    }
}
