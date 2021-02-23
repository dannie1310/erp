<?php


namespace App\Observers\ACARREOS\SCA_CONFIGURACION;


use App\Models\ACARREOS\SCA_CONFIGURACION\Tag;

class TagObserver
{
    /**
     * @param Tag $tag
     */
    public function creating(Tag $tag)
    {
        $tag->fecha_registro = date('Y-m-d H:i:s');
        $tag->estado = 1;
    }
}
