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

    public function updating(Tiro $tiro)
    {
        if($tiro->getOriginal('Estatus') == $tiro->Estatus)
        {
            abort(400, "El tiro se encuentra ".$tiro->estado_format." previamente.");
        }
    }
}
