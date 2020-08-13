<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoEmpresa;
use League\Fractal\TransformerAbstract;

class TipoEmpresaTransformer extends TransformerAbstract
{
    public function transform(CtgTipoEmpresa $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}
