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
use App\Models\CADECO\Transaccion;

class DebitoObserver extends TransaccionObserver
{
    /**
     * @param Debito $debito
     *  @throws \Exception
     */
    public function creating(Transaccion $debito)
    {
        parent::creating($debito);
        $debito->estado = 1;
        $debito->id_moneda = Obra::query()->find(Context::getIdObra())->id_moneda;
        $debito->opciones = 1;
        $debito->tipo_transaccion = 84;
        $debito->vencimiento = $debito->cumplimiento;
    }

    public function updating(Debito $debito)
    {
        $debito->vencimiento = $debito->cumplimiento;
    }
}