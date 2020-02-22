<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/02/2020
 * Time: 06:41 PM
 */

namespace App\Observers\CTPQ;


use App\Models\CTPQ\PolizaMovimiento;

class PolizaMovimientoObserver
{
    public function updated(PolizaMovimiento $movimiento)
    {
        if($movimiento->getOriginal("Referencia") !=  $movimiento->Referencia){
            $movimiento->logs()->create([
                "id_movimiento"=>$movimiento->Id,
                "id_empresa"=>666,
                "empresa"=>666,
                "id_campo"=>2,
                "valor_original"=>$movimiento->getOriginal("Referencia"),
                "valor_modificado"=>$movimiento->Referencia,
            ]);
        }
        if($movimiento->getOriginal("Concepto") !=  $movimiento->Concepto){
            $movimiento->logs()->create([
                "id_movimiento"=>$movimiento->Id,
                "id_empresa"=>666,
                "empresa"=>666,
                "id_campo"=>3,
                "valor_original"=>$movimiento->getOriginal("Concepto"),
                "valor_modificado"=>$movimiento->Concepto,
            ]);
        }
    }
}