<?php


namespace App\Observers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;

class ArchivoObserver
{
    /**
     * @param Archivo $archivo
     */
    public function creating(Archivo $archivo)
    {

    }

    /**
     * @param Archivo $archivo
     */
    public function updating(Archivo $archivo){
        $archivo->usuario_registro = auth()->id();
        $archivo->fecha_hora_registro = date('Y-m-d H:i:s');
    }
}
