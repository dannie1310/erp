<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/07/2020
 * Time: 08:11 PM
 */

namespace App\Observers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;

class EFOSObserver
{
    public function created(EFOS $efos){
        $efos->cambios()->create([
            "id_efo"=> $efos->id,
            "estado_final"=>$efos->estado,
            "id_procesamiento_efos"=>$efos->id_procesamiento_registro
        ]);
    }

    public function updated(EFOS $efos){
        if($efos->getOriginal("estado") !=  $efos->estado){
            $efos->cambios()->create([
                "id_efo"=> $efos->id,
                "estado_inicial"=>$efos->getOriginal("estado"),
                "estado_final"=>$efos->estado,
                "id_procesamiento_efos"=>$efos->id_procesamiento_actualizacion
            ]);
        }
    }
}
