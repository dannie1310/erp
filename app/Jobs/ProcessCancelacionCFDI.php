<?php

namespace App\Jobs;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCancelacionCFDI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cfdi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CFDSAT $cfdi)
    {
        $this->cfdi = $cfdi;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->cfdi->validaVigencia();
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}
