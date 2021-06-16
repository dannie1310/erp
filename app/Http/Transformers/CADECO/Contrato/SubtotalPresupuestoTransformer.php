<?php


namespace App\Http\Transformers\CADECO\Contrato;


use League\Fractal\TransformerAbstract;

class SubtotalPresupuestoTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [

            'moneda' => $model["moneda"],
            'subtotal_format' => $model["subtotal_format"],
        ];
    }

}
