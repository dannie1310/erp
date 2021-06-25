<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\CtgTipoFecha;
use League\Fractal\TransformerAbstract;

class TipoFechaTransformer extends TransformerAbstract
{
    public function transform(CtgTipoFecha $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }
}
