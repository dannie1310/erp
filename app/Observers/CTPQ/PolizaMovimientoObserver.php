<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/02/2020
 * Time: 06:41 PM
 */

namespace App\Observers\CTPQ;


use App\Models\CTPQ\PolizaMovimiento;
use App\Models\CTPQ\Empresa;

class PolizaMovimientoObserver
{
    public function updated(PolizaMovimiento $movimiento)
    {
        $base_datos = config('database.connections.cntpq.database');
        if($movimiento->getOriginal("Referencia") !=  $movimiento->Referencia){
            $movimiento->logs()->create([
                "id_movimiento"=>$movimiento->Id,
                "id_poliza"=>$movimiento->poliza->Id,
                "id_empresa"=>Empresa::getIdEmpresa($base_datos),
                "empresa"=>Empresa::getNombreEmpresa($base_datos),
                "id_campo"=>2,
                "valor_original"=>$movimiento->getOriginal("Referencia"),
                "valor_modificado"=>$movimiento->Referencia,
                "bd_contpaq" => $base_datos
            ]);
        }
        if($movimiento->getOriginal("Concepto") !=  $movimiento->Concepto){
            $movimiento->logs()->create([
                "id_movimiento"=>$movimiento->Id,
                "id_poliza"=>$movimiento->poliza->Id,
                "id_empresa"=>Empresa::getIdEmpresa($base_datos),
                "empresa"=>Empresa::getNombreEmpresa($base_datos),
                "id_campo"=>3,
                "valor_original"=>$movimiento->getOriginal("Concepto"),
                "valor_modificado"=>$movimiento->Concepto,
                "bd_contpaq" => $base_datos
            ]);
        }
        if($movimiento->getOriginal("NumMovto") !=  $movimiento->NumMovto &&  $movimiento->NumMovto > 0){
            $original =$movimiento->getOriginal("NumMovto");
            if($original <0){
                $original = $original*(-1);
            }
            $movimiento->logs()->create([
                "id_movimiento"=>$movimiento->Id,
                "id_poliza"=>$movimiento->poliza->Id,
                "id_empresa"=>Empresa::getIdEmpresa($base_datos),
                "empresa"=>Empresa::getNombreEmpresa($base_datos),
                "id_campo"=>4,
                "valor_original"=>$original,
                "valor_modificado"=>$movimiento->NumMovto,
                "bd_contpaq" => $base_datos
            ]);
        }
    }
}