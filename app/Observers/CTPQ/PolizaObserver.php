<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/02/2020
 * Time: 06:41 PM
 */

namespace App\Observers\CTPQ;


use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\Poliza;

class PolizaObserver
{
    /**
     * @param Poliza $poliza
     */
    public function creating(Poliza $poliza)
    {

    }

    public function updating(Poliza $poliza)
    {
        $poliza->validarReglas();
    }

    public function updated(Poliza $poliza)
    {
        if($poliza->getOriginal("Concepto") !=  $poliza->Concepto){
            $poliza->createLog(1,$poliza->getOriginal("Concepto"), $poliza->Concepto);
        }
        $date = date_create($poliza->getOriginal("Fecha"));
        if(date_format($date, "d/m/Y") != $poliza->fecha_format) {
            $poliza->createLog(8,$poliza->getOriginal("Fecha"),$poliza->Fecha);
        }
        if($poliza->getOriginal("Ejercicio") != $poliza->Ejercicio) {
            $poliza->createLog(6, $poliza->getOriginal("Ejercicio"),$poliza->Ejercicio);
        }
        if($poliza->getOriginal("Periodo") != $poliza->Periodo) {
            $poliza->createLog(7,$poliza->getOriginal("Periodo"), $poliza->Periodo);
        }
        if($poliza->getOriginal("TipoPol") != $poliza->TipoPol) {
            $poliza->createLog(9,$poliza->getOriginal("TipoPol"),$poliza->TipoPol);
        }
        if($poliza->getOriginal("Folio") != $poliza->Folio) {
            $poliza->createLog( 10,$poliza->getOriginal("Folio"),$poliza->Folio);
        }
        if($poliza->getOriginal("Cargos") != $poliza->Cargos) {
            $poliza->createLog( 11,$poliza->getOriginal("Cargos"),$poliza->Cargos);
        }
        if($poliza->getOriginal("Abonos") != $poliza->Abonos) {
            $poliza->createLog( 12,$poliza->getOriginal("Abonos"),$poliza->Abonos);
        }
    }
}
