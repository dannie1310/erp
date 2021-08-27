<?php

namespace App\Console\Commands;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService;
use Illuminate\Console\Command;

class LiberarCotizaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'libera:cotizaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Liberar cotizaciones cuya invitación ha alcanzado su fecha de cierre';

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
        $servicio = new InvitacionService(new Invitacion());
        $servicio->liberaCotizaciones();
    }
}
