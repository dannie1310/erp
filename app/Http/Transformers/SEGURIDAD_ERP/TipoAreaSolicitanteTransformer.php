<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\TipoAreaSolicitante;
use League\Fractal\TransformerAbstract;

class TipoAreaSolicitanteTransformer extends TransformerAbstract
{
    public function transform(TipoAreaSolicitante $model)
    {
        return[
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }
}
