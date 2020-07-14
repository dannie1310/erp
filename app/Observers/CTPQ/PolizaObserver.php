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
        if($poliza->getOriginal("Concepto") !=  $poliza->Concepto){
            $poliza->logs()->create([
                "id_poliza"=>$poliza->Id,
                "id_empresa"=>Empresa::getIdEmpresa($base_datos),
                "empresa"=>Empresa::getNombreEmpresa($base_datos),
                "id_campo"=>1,
                "valor_original"=>$poliza->getOriginal("Concepto"),
                "valor_modificado"=>$poliza->Concepto,
                "bd_contpaq" => $base_datos
            ]);
        }
    }

}