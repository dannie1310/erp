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
    /**
     * @param PolizaMovimiento $movimiento
     */
    public function creating(PolizaMovimiento $movimiento)
    {
       /* $this->Id;
        $this->RowVersion;*/
        $this->TimeStamp = date('Y-m-d H:i:s');
        //$this->Guid;
    }

    public function created(PolizaMovimiento $movimiento)
    {
        $movimiento->createLog(2, null, $movimiento->Referencia);
        $movimiento->createLog(3, null, $movimiento->Concepto);
        $movimiento->createLog(4, null, $movimiento->NumMovto);
        $movimiento->createLog(13,null, $movimiento->Ejercicio);
        $movimiento->createLog(14,null, $movimiento->Periodo);
        $movimiento->createLog(15,null, $movimiento->TipoPol);
        $movimiento->createLog(16,null, $movimiento->Folio);
        $movimiento->createLog(17,null, $movimiento->IdCuenta);
        $movimiento->createLog(18,null, $movimiento->TipoMovto);
        $movimiento->createLog(19,null, $movimiento->Importe);
        $movimiento->createLog(20,null, $movimiento->Fecha);
    }

    public function updated(PolizaMovimiento $movimiento)
    {
        if($movimiento->getOriginal("Referencia") !=  $movimiento->Referencia){
            $movimiento->createLog(2,$movimiento->getOriginal("Referencia"), $movimiento->Referencia);
        }
        if($movimiento->getOriginal("Concepto") !=  $movimiento->Concepto){
            $movimiento->createLog(3,$movimiento->getOriginal("Concepto"), $movimiento->Concepto);
        }
        if($movimiento->getOriginal("NumMovto") !=  $movimiento->NumMovto){
            $movimiento->createLog(4,$movimiento->getOriginal("NumMovto"), $movimiento->NumMovto);
        }
        if($movimiento->getOriginal("Ejercicio") !=  $movimiento->Ejercicio){
            $movimiento->createLog(13,$movimiento->getOriginal("Ejercicio"), $movimiento->Ejercicio);
        }
        if($movimiento->getOriginal("Periodo") !=  $movimiento->Periodo){
            $movimiento->createLog(14,$movimiento->getOriginal("Periodo"), $movimiento->Periodo);
        }
        if($movimiento->getOriginal("TipoPol") !=  $movimiento->TipoPol){
            $movimiento->createLog(15,$movimiento->getOriginal("TipoPol"), $movimiento->TipoPol);
        }
        if($movimiento->getOriginal("Folio") !=  $movimiento->Folio){
            $movimiento->createLog(16,$movimiento->getOriginal("Folio"), $movimiento->Folio);
        }
        if($movimiento->getOriginal("IdCuenta") !=  $movimiento->IdCuenta){
            $movimiento->createLog(17,$movimiento->getOriginal("IdCuenta"), $movimiento->IdCuenta);
        }
        if($movimiento->getOriginal("TipoMovto") !=  $movimiento->TipoMovto){
            $movimiento->createLog(18,$movimiento->getOriginal("TipoMovto"), $movimiento->TipoMovto);
        }
        if($movimiento->getOriginal("Importe") !=  $movimiento->Importe){
            $movimiento->createLog(19,$movimiento->getOriginal("Importe"), $movimiento->Importe);
        }
        if($movimiento->getOriginal("Fecha") !=  $movimiento->Fecha){
            $movimiento->createLog(20,$movimiento->getOriginal("Fecha"), $movimiento->Fecha);
        }
    }

    public function deleted(PolizaMovimiento $movimiento)
    {
        $movimiento->createLog(2, $movimiento->getOriginal("Referencia"),null);
        $movimiento->createLog(3, $movimiento->getOriginal("Concepto"),null);
        $movimiento->createLog(4, $movimiento->getOriginal("NumMovto"), null);
        $movimiento->createLog(13,$movimiento->getOriginal("Ejercicio"),null);
        $movimiento->createLog(14,$movimiento->getOriginal("Periodo"), null);
        $movimiento->createLog(15,$movimiento->getOriginal("TipoPol"), null);
        $movimiento->createLog(16,$movimiento->getOriginal("Folio"), null);
        $movimiento->createLog(17,$movimiento->getOriginal("IdCuenta"), null);
        $movimiento->createLog(18,$movimiento->getOriginal("TipoMovto"), null);
        $movimiento->createLog(19,$movimiento->getOriginal("Importe"), null);
        $movimiento->createLog(20,$movimiento->getOriginal("Fecha"), null);
        $movimiento->createLog(21,$movimiento->getOriginal("IdPoliza"), null);
    }
}
