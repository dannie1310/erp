<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\Tiro;

class TiroObserver
{
    /**
     * @param Tiro $tiro
     */
    public function creating(Tiro $tiro)
    {
        $tiro->validarRegistro();
        $tiro->FechaAlta = date('Y-m-d');
        $tiro->HoraAlta = date('H:i:s');
        $tiro->Estatus = 1;
        $tiro->usuario_registro = auth()->id();
        $tiro->IdProyecto = 1;
    }
}
