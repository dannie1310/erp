<?php


namespace App\Http\Transformers\CADECO\Compras;


use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\CtgFormaPagoCredito;

class FormaPagoCreditoTransformer extends TransformerAbstract
{

    public function transform(CtgFormaPagoCredito $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }
}
