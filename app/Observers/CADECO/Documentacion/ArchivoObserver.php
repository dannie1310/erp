<?php


namespace App\Observers\CADECO\Documentacion;


use App\Models\CADECO\Documentacion\Archivo;

class ArchivoObserver
{
    /**
     * @param Archivo $archivo
     */
    public function creating(Archivo $archivo)
    {
        $archivo->usuario_registro = auth()->id();
        $archivo->fecha_hora_registro = date('Y-m-d H:i:s');
    }
}
