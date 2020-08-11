<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(Empresa $model)
    {
        return [
            'id' => $model->getKey(),
        ];
    }
}
