<?php

namespace App\Console\Commands;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDISATNominaService;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService;
use Illuminate\Console\Command;

class ProcesaCFDISATNomina extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receptores:llenaDatosIntranet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena datos intranet';

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
        $servicio = new CFDISATNominaService(new CFDISATNomina());
        //$servicio->procesaDirectorioZIPCFDI();
        //$servicio->llenaSolicitaGxCRel();
        //$servicio->reprocesaLlenadoEmisorNominas();
        $servicio->llenaDatosAccesoSistemas();
        //$servicio->llenaDatosIntranet();
    }
}
