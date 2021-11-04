<?php

namespace App\Jobs;

use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaContpaqProvedorSat;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCuentaProveedorPartida;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAsociacionCuentasContpaqProveedoresSAT implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $solicitud_asociacion_partida;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SolicitudAsociacionCuentaProveedorPartida $partida)
    {
        $this->solicitud_asociacion_partida = $partida;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->solicitud_asociacion_partida->procesarAsociacion();
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}
