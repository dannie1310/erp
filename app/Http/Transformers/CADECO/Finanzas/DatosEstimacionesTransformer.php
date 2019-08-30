<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DatosEstimaciones;
use League\Fractal\TransformerAbstract;

class DatosEstimacionesTransformer extends TransformerAbstract
{
    public function transform(DatosEstimaciones $model){
        return $model->toArray();
    }

}