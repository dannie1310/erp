<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\Origen;

class OrigenObserver
{
    /**
     * @param Origen $origen
     */
    public function creating(Origen $origen)
    {
        $origen->validarRegistro();
        $origen->FechaAlta = date('Y-m-d');
        $origen->HoraAlta = date('H:i:s');
        $origen->usuario_registro = auth()->id();
        $origen->Clave = 'B';
        $origen->IdProyecto = 1;
        $origen->Estatus = 1;
    }

    public function updating(Origen $origen)
    {
        $origen->usuario_edito = auth()->id();
    }
}
