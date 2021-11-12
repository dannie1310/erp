<?php


namespace App\Http\Transformers\CADECO\Compras;


use League\Fractal\TransformerAbstract;

class DetalleEstadoCotizacionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'titulos' => $model["titulos"],
            'partidas' => $model["partidas"]
        ];
    }
}
