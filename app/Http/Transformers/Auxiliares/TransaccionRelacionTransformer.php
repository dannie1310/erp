<?php

namespace App\Http\Transformers\Auxiliares;


use League\Fractal\TransformerAbstract;

class TransaccionRelacionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->id_transaccion,
            'opcion'=>$model::OPCION,
            'tipo'=>$model::TIPO,
        ];
    }

}
