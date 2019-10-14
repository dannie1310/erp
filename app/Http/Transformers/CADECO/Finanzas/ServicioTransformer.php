<?php


namespace App\Http\Transformers\CADECO\Finanzas;

use App\Models\CADECO\Familia;
use App\Models\CADECO\Material;
use League\Fractal\TransformerAbstract;

class ServicioTransformer extends TransformerAbstract
{
    public function transform(Familia $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'unidad' => $model->unidad,
            'tipo_material' => $model->tipo_material,
            'tipo_material_descripcion' => $model->tipo_material_descripcion,
            'descripcion_padre' => $model->descripcion_padre
        ];
    }
}
