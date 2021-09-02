<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Compras\Exclusion;
use League\Fractal\TransformerAbstract;

class ExclusionTransformer extends TransformerAbstract
{
    public function transform(Exclusion $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => $model->descripcion,
            'unidad' => $model->unidad,
            'cantidad' => $model->cantidad,
            'cantidad_format' => $model->cantidad_format,
            'precio_unitario' => $model->precio_unitario,
            'precio_format' => $model->precio_format,
            'id_moneda' => $model->id_moneda,
            'moneda' => $model->nombre_moneda
        ];
    }
}
