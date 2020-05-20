<?php

namespace App\Jobs;

use App\Services\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBusquedaDiferenciasPolizas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $servicio_busqueda;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DiferenciaService $servicio_busqueda)
    {
        $this->servicio_busqueda = $servicio_busqueda;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->servicio_busqueda->procesarBusquedaDiferencias();
    }
}
