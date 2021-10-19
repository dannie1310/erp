<?php

namespace App\Jobs;

use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaContpaqProvedorSat;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAsociacionCuentasContpaqProveedoresSAT implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cuenta_proveedor;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CuentaContpaqProvedorSat $cuenta_proveedor)
    {
        $this->cuenta_proveedor = $cuenta_proveedor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->cuenta_proveedor->cuenta->procesarAsociacionProveedor();
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}
