<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CentroCosto;
use League\Fractal\TransformerAbstract;

class CentroCostoTransformer extends TransformerAbstract
{
    public function transform(CentroCosto $model){
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->Descripcion
        ];
    }
}
