<?php


namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\DatosContables;
use League\Fractal\TransformerAbstract;

class DatosContablesTransformer extends TransformerAbstract
{

    public function transform(DatosContables $model){
        return $model->toArray();
    }
}