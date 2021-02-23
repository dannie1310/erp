<?php

namespace App\Jobs;

use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAsociacionCFDI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $solicitud_partida;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SolicitudAsociacionCFDIPartida $solicitud_partida)
    {
        $this->solicitud_partida = $solicitud_partida;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->solicitud_partida->procesarAsociacionCFDI();
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}
