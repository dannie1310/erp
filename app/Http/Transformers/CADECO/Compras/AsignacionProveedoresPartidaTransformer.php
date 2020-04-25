<?php


namespace App\Http\Transformers\CADECO\Compras;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;

class AsignacionProveedoresPartidaTransformer extends TransformerAbstract
{

    public function transform(AsignacionProveedoresPartida $model)
    {
        return [
            'id' => (int) $model->getKey(),
        ];
    }
}
