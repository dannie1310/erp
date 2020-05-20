<?php

namespace App\Jobs;

use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Busqueda;
use App\Services\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBusquedaDiferenciasPolizas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $busqueda;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Busqueda $busqueda)
    {
        $this->busqueda = $busqueda;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->busqueda->procesarBusquedaDiferencias();
    }
}
