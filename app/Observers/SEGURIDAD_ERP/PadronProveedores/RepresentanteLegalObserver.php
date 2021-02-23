<?php


namespace App\Observers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\RepresentanteLegal;

class RepresentanteLegalObserver
{
    public function deleting(RepresentanteLegal $representanteLegal){
        if($representanteLegal->archivo) {
            $representanteLegal->archivo->delete();
        }
    }
}
