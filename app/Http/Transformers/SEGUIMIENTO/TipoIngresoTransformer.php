<?php


namespace App\Http\Transformers\SEGUIMIENTO;


use App\Models\REPSEG\FinDimTipoIngreso;
use League\Fractal\TransformerAbstract;

class TipoIngresoTransformer extends TransformerAbstract
{
    public function transform(FinDimTipoIngreso $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'tipo_ingreso' => $model->tipo_ingreso
        ];
    }
}
