<?php


namespace App\Http\Transformers\ACTIVO_FIJO;

use League\Fractal\TransformerAbstract;

class UsuarioUbicacionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->idUbicacion,
        ];
    }
}
