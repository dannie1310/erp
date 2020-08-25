<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgArea;

class CtgAreaTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
    ];

    public function transform(CtgArea $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
        ];
    }
}