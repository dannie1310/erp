<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgGiro;
use League\Fractal\TransformerAbstract;

class GiroTransformer extends TransformerAbstract
{
    public function transform(CtgGiro $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}
