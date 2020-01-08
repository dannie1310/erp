<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfosLog;
use League\Fractal\TransformerAbstract;

class CtgEfosLogTransformer extends TransformerAbstract
{

    public function transform(CtgEfosLog $model)
    {
        return[
            'id' => $model->getKey()
        ];
    }

}
