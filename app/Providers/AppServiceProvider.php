<?php

namespace App\Providers;

use App\Models\CADECO\Compras\EntradaEliminada;
use App\Models\CADECO\Compras\SalidaEliminada;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLog;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Observers\CADECO\Compras\EntradaEliminadaObserver;
use App\Observers\CADECO\Compras\SalidaEliminadaObserver;
use App\Observers\CADECO\Finanzas\ConfiguracionEstimacionObserver;
use App\Observers\CADECO\Finanzas\CuentaBancariaEmpresaObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaLogObserver;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaObserver;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Observers\CADECO\Finanzas\DistribucionRecursoRemesaPartidaObserver;
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
        /**
         * Compras
         */
        EntradaEliminada::observe(EntradaEliminadaObserver::class);
        SalidaEliminada::observe(SalidaEliminadaObserver::class);

        /**
         * Finanzas
         */
        ConfiguracionEstimacion::observe(ConfiguracionEstimacionObserver::class);
        CuentaBancariaEmpresa::observe(CuentaBancariaEmpresaObserver::class);
        DistribucionRecursoRemesa::observe(DistribucionRecursoRemesaObserver::class);
        DistribucionRecursoRemesaLog::observe(DistribucionRecursoRemesaLogObserver::class);
        DistribucionRecursoRemesaPartida::observe(DistribucionRecursoRemesaPartidaObserver::class);
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
