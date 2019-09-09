<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:04 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Debito;
use App\Models\CADECO\Obra;

class DebitoObserver
{
    /**
     * @param Debito $debito
     */
    public function creating(Debito $debito)
    {
        $debito->estado = 1;
        $debito->id_moneda = Obra::query()->find(Context::getIdObra())->id_moneda;
        $debito->opciones = 1;
        $debito->tipo_transaccion = 84;
        $debito->vencimiento = $debito->cumplimiento;
        $debito->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $debito->FechaHoraRegistro = date('Y-m-d h:i:s');
        $debito->id_obra = Context::getIdObra();
    }

    public function updating(Debito $debito)
    {
        $debito->vencimiento = $debito->cumplimiento;
    }
}