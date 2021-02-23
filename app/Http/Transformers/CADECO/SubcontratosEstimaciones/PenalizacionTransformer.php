<?php

namespace App\Http\Transformers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;
use League\Fractal\TransformerAbstract;

class PenalizacionTransformer extends TransformerAbstract
{
    public function transform(Penalizacion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'importe' => $model->importe,
            'importe_format' => $model->importe_format,
            'importe_disponible' => $model->importe_disponible,
            'concepto' => $model->concepto,
            'importe_disponible_format' => $model->importe_disponible_format,
            'liberada' => ($model->liberaciones->first()) ? true : false
        ];
    }
}