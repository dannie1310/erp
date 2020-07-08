<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use League\Fractal\TransformerAbstract;

class ProveedorSATTransformer extends TransformerAbstract
{
    public function transform(ProveedorSAT $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }
}
