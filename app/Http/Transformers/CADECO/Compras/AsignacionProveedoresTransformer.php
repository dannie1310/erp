<?php


namespace App\Http\Transformers\CADECO\Compras;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedores;

class AsignacionProveedoresTransformer extends TransformerAbstract
{

    public function transform(AsignacionProveedores $model)
    {
        return [
            'id' => (int) $model->getKey(),
        ];
    }
}
