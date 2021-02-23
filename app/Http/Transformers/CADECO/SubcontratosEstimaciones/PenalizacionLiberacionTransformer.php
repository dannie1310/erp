<?php


namespace App\Http\Transformers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;
use League\Fractal\TransformerAbstract;

class PenalizacionLiberacionTransformer extends TransformerAbstract
{
    public function transform(PenalizacionLiberacion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'importe' => $model->importe,
            'importe_format' => $model->importe_format,
            'concepto' => $model->concepto
        ];
    }
}