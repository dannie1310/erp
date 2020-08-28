<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoPersonalidad;
use League\Fractal\TransformerAbstract;

class CtgTipoPersonalidadTransformer extends TransformerAbstract
{
    public function transform(CtgTipoPersonalidad $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}
