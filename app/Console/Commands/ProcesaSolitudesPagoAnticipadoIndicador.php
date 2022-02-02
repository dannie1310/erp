<?php

namespace App\Console\Commands;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService;
use App\Services\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicadaService;
use Illuminate\Console\Command;

class ProcesaSolitudesPagoAnticipadoIndicador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'procesa:solicitudesPagoParaIndicador';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'procesar solicitudes de pago de las obras para obtener el indicador de aplicación';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $servicio = new SolicitudPagoAplicadaService(new SolicitudPagoAplicada());
        $servicio->procesaSolicitudesPagoParaIndicador();
    }
}
