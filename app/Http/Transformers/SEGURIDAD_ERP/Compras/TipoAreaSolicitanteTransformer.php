<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante;
use League\Fractal\TransformerAbstract;

class TipoAreaSolicitanteTransformer extends TransformerAbstract
{
    public function transform(CtgAreaSolicitante $model)
    {
        return[
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'descripcion_corta' => $model->descripcion_corta,
        ];
    }
}
