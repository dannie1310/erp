<?php


namespace App\Http\Controllers\v1\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;
use League\Fractal\TransformerAbstract;

class PenalizacionLiberacionTransformer extends TransformerAbstract
{
    public function transform(PenalizacionLiberacion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion
        ];
    }
}