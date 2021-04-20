<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\CamionImagen;

class CamionImagenObserver
{
    /**
     * @param CamionImagen $imagen
     */
    public function creating(CamionImagen $imagen)
    {
        $imagen->Imagen = $imagen->limpiarStringImagen();
        $imagen->buscarImagenesCamion();
        $imagen->Estatus = 1;
    }

    public function created(CamionImagen $imagen)
    {
        $imagen->historicoImagen();
    }

    public function updated(CamionImagen $imagen)
    {
        $imagen->historicoImagen();
    }
}
