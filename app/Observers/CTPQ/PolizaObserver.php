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
        $empresa = Empresa::where("AliasBDD","=", $base_datos);
        dd($empresa, $empresa->IdContpaq, $empresa->Nombre);
        if($poliza->getOriginal("Concepto") !=  $poliza->Concepto){
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre,1,$poliza->getOriginal("Concepto"), $poliza->Concepto);
        }
        if($poliza->getOriginal("Fecha") != $this->Fecha) {
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre,8,$poliza->getOriginal("Fecha"),$this->Fecha);
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre,6, $poliza->getOriginal("Ejercicio"),$this->Ejercicio);
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre,7,$poliza->getOriginal("Periodo"), $this->Periodo);
        }
        if($poliza->getOriginal("TipoPol") != $this->TipoPol) {
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre,9,$poliza->getOriginal("TipoPol"),$this->TipoPol);
        }
        if($poliza->getOriginal("Folio") != $this->Folio) {
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre, 10,$poliza->getOriginal("Folio"),$this->Folio);
        }
        if($poliza->getOriginal("Cargos") != $this->Cargos) {
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre, 11,$poliza->getOriginal("Cargos"),$this->Cargos);
        }
        if($poliza->getOriginal("Abonos") != $this->Abonos) {
            $poliza->createLog($empresa->IdContpaq, $empresa->Nombre, 12,$poliza->getOriginal("Abonos"),$this->Abonos);
        }
    }
}
