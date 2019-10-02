<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgEstadoLayoutPago;
use League\Fractal\TransformerAbstract;

class CtgEstadoLayoutPagoTransformer extends TransformerAbstract
{
    public function transform(CtgEstadoLayoutPago $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }

}