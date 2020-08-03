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
    public function updating(Poliza $poliza)
    {


    }

    public function updated(Poliza $poliza)
    {
        $base_datos = config('database.connections.cntpq.database');
        $empresa = Empresa::where("AliasBDD","=", $base_datos)->first();
        if($poliza->getOriginal("Concepto") !=  $poliza->Concepto){
            $poliza->createLog(1,$poliza->getOriginal("Concepto"), $poliza->Concepto);
        }
        if($poliza->getOriginal("Fecha") != $poliza->Fecha) {
            $poliza->createLog(8,$poliza->getOriginal("Fecha"),$poliza->Fecha);
            $poliza->createLog(6, $poliza->getOriginal("Ejercicio"),$poliza->Ejercicio);
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
