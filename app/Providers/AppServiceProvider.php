<?php

namespace App\Providers;

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
        \App\Models\Pembelian::observe(\App\Observers\PembelianObserver::class);
        \App\Models\SetoranModal::observe(\App\Observers\SetoranModalObserver::class);
        \App\Models\Penjualan::observe(\App\Observers\PenjualanObserver::class);
        \App\Models\KematianIkan::observe(\App\Observers\KematianIkanObserver::class);
        \App\Models\Persediaan::observe(\App\Observers\PersediaanObserver::class);
        \App\Models\PemberianPakan::observe(\App\Observers\PemberianPakanObserver::class);
        \App\Models\BiayaOperasional::observe(\App\Observers\BiayaOperasionalObserver::class);
    }
}
