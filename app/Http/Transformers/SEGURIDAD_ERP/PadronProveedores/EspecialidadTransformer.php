<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEspecialidad;
use League\Fractal\TransformerAbstract;

class EspecialidadTransformer extends TransformerAbstract
{
    public function transform(CtgEspecialidad $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}
