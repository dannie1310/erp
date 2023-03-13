<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinDimIngresoPartida;
use League\Fractal\TransformerAbstract;

class IngresoPartidaTransformer extends TransformerAbstract
{
    public function transform(FinDimIngresoPartida $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'partida' => $model->partida,
            'nombre_operador' => $model->nombre_operador
        ];
    }
}
