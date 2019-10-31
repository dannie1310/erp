<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:43 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\LiberacionFondoGarantia;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;

class LiberacionFondoGarantiaObserver extends TransaccionObserver
{
    /**
     * @param LiberacionFondoGarantia $fondoGarantia
     *  @throws \Exception
     */
    public function creating(Transaccion $fondoGarantia)
    {
        parent::creating($fondoGarantia);
        $subcontrato = Subcontrato::find($fondoGarantia->id_antecedente);
        $fondoGarantia->tipo_transaccion = 53;
        $fondoGarantia->opciones = 0;
        $fondoGarantia->estado = 1;
        $fondoGarantia->id_empresa = $subcontrato->id_empresa;
        $fondoGarantia->id_moneda = $subcontrato->id_moneda;
        $fondoGarantia->saldo = $fondoGarantia->monto;
    }

    public function updating(LiberacionFondoGarantia $fondoGarantia)
    {
        if($fondoGarantia->saldo != $fondoGarantia->monto){
            throw new \Exception('La transacción de liberación no puede ser cancelada, su saldo ya ha sido afectado');
        }
    }
}