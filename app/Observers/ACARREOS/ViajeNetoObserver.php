<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\ViajeNeto;

class ViajeNetoObserver
{
    /**
     * @param ViajeNeto $viajeNeto
     */
    public function creating(ViajeNeto $viajeNeto)
    {
        $viajeNeto->IdArchivoCargado = 0;
        $viajeNeto->FechaCarga = date('Y-m-d');
        $viajeNeto->HoraCarga = date('H:i:s');
        $viajeNeto->IdProyecto = 1;
        $viajeNeto->Estatus = 0;
    }
}
