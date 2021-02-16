<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\Tag;

class TagObserver
{
    /**
     * @param Tag $tag
     */
    public function creating(Tag $tag)
    {
        $tag->fecha_asignacion = date('Y-m-d H:i:s');
        $tag->idproyecto = 1;
        $tag->estado = 1;
    }
}
