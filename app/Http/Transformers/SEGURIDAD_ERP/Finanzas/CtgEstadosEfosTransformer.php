<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use League\Fractal\TransformerAbstract;

class CtgEstadosEfosTransformer extends TransformerAbstract
{
    public function transform(CtgEstadosEfos $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }

}
