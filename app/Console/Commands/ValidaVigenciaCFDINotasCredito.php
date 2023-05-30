<?php

namespace App\Console\Commands;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService;
use Illuminate\Console\Command;

class ValidaVigenciaCFDINotasCredito extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'valida:vigenciaCFDIPago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar vigencia de CFDI tipo N';

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
        $servicio = new CFDSATService(new CFDSAT());
        $servicio->detectarCancelacionesREP();
    }
}
