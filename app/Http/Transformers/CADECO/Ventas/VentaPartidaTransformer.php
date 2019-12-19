<?php


namespace App\Http\Transformers\CADECO\Ventas;


use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\VentaPartida;
use League\Fractal\TransformerAbstract;

class VentaPartidaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'material'
    ];

    public function transform(VentaPartida $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'unidad' => $model->unidad,
            'cantidad_format' => $model->cantidad_format,
            'cantidad_decimal' => $model->cantidad_decimal,
            'precio_unitario' => $model->precio_unitario_format,
            'importe' => $model->importe_format
        ];
    }

    /**
     * @param VentaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(VentaPartida $model)
    {
        if ($usuario = $model->material) {
            return $this->item($usuario, new MaterialTransformer);
        }
        return null;
    }
}
