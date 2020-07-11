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
    public function creating(EFOS $efos){
        $efos->cambios()->create([
            "id_efo"=> $efos->id,
            "estado_final"=>$efos->estado
        ]);
    }

}