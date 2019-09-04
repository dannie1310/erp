<?php

namespace App\Providers;

use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Observers\CADECO\Finanzas\CuentaBancariaEmpresaObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaObserver;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DistribucionRecursoRemesa::observe(DistribucionRecursoRemesaObserver::class);
        CuentaBancariaEmpresa::observe(CuentaBancariaEmpresaObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
