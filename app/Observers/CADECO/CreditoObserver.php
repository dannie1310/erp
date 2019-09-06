<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:57 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Credito;
use App\Models\CADECO\Obra;

class CreditoObserver
{
    /**
     * @param Credito $credito
     */
    public function creating(Credito $credito)
    {
        $credito->estado = 1;
        $credito->id_moneda = Obra::query()->find(Context::getIdObra())->id_moneda;
        $credito->opciones = 1;
        $credito->tipo_transaccion = 83;
        $credito->vencimiento = $credito->cumplimiento;
    }

    public function updating(Credito $credito)
    {
        $credito->vencimiento = $credito->cumplimiento;
    }
}