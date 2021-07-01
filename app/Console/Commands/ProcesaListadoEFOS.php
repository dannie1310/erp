<?php

namespace App\Console\Commands;

use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgEfosService;
use Illuminate\Console\Command;

class ProcesaListadoEFOS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'procesa:listadoEFOS';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar el procesamiento de listado de EFOS emitido por el SAT';

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
        $servicio = new CtgEfosService(new CtgEfos());
        $resultado = $servicio->procesaURLCSV();
    }
}
