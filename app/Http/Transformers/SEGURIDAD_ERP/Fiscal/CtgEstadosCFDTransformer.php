<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\CtgEstadoCFD;
use League\Fractal\TransformerAbstract;

class CtgEstadosCFDTransformer extends TransformerAbstract
{
    public function transform(CtgEstadoCFD $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}
