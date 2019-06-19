<?php


namespace App\Http\Transformers\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\SubcontratosEstimaciones\Estimacion;
use League\Fractal\TransformerAbstract;

class SubcontratoEstimacionTrasnformer extends TransformerAbstract
{
    public function transform(Estimacion $model)
    {
        return $model->toArray();
    }
}