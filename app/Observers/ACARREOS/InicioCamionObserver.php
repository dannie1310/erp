<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\InicioCamion;

class InicioCamionObserver
{
    /**
     * @param InicioCamion $inicioCamion
     */
    public function creating(InicioCamion $inicioCamion)
    {
        $inicioCamion->FechaCarga = date('Y-m-d H:i:s');
        $inicioCamion->estatus = 0;
    }
}
