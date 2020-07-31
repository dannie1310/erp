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
        $empresa = Empresa::where("AliasBDD","=", $base_datos);
        if($movimiento->getOriginal("Referencia") !=  $movimiento->Referencia){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,2,$movimiento->getOriginal("Referencia"), $movimiento->Referencia);
        }
        if($movimiento->getOriginal("Concepto") !=  $movimiento->Concepto){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,3,$movimiento->getOriginal("Concepto"), $movimiento->Concepto);
        }
        if($movimiento->getOriginal("NumMovto") !=  $movimiento->NumMovto){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,4,$movimiento->getOriginal("NumMovto"), $movimiento->NumMovto);
        }
        if($movimiento->getOriginal("Ejercicio") !=  $movimiento->Ejercicio){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,13,$movimiento->getOriginal("Ejercicio"), $movimiento->Ejercicio);
        }
        if($movimiento->getOriginal("Periodo") !=  $movimiento->Periodo){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,14,$movimiento->getOriginal("Periodo"), $movimiento->Periodo);
        }
        if($movimiento->getOriginal("TipoPol") !=  $movimiento->TipoPol){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,15,$movimiento->getOriginal("TipoPol"), $movimiento->TipoPol);
        }
        if($movimiento->getOriginal("Folio") !=  $movimiento->Folio){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,16,$movimiento->getOriginal("Folio"), $movimiento->Folio);
        }
        if($movimiento->getOriginal("IdCuenta") !=  $movimiento->IdCuenta){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,17,$movimiento->getOriginal("IdCuenta"), $movimiento->IdCuenta);
        }
        if($movimiento->getOriginal("TipoMovto") !=  $movimiento->TipoMovto){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,18,$movimiento->getOriginal("IdCuenta"), $movimiento->IdCuenta);
        }
        if($movimiento->getOriginal("Importe") !=  $movimiento->Importe){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,19,$movimiento->getOriginal("Importe"), $movimiento->Importe);
        }
        if($movimiento->getOriginal("Fecha") !=  $movimiento->Fecha){
            $movimiento->createLog($empresa->IdContpaq, $empresa->Nombre,20,$movimiento->getOriginal("Fecha"), $movimiento->Fecha);
        }
    }
}
