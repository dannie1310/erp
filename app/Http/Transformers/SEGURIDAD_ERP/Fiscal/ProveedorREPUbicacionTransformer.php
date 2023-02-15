<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREPUbicacion;
use League\Fractal\TransformerAbstract;

class ProveedorREPUbicacionTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(ProveedorREPUbicacion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_responsable' => $model->id_responsable,
            'id_administrador' => $model->id_administrador,
            'administrador' => $model->administrador,
            'responsable' => $model->responsable,
            'correo_administrador' => $model->correo_administrador,
            'correo_responsable' => $model->correo_responsable,
            'ubicacion' => $model->ubicacion ,
        ];
    }
}
