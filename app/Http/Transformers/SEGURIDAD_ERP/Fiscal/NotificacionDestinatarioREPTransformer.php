<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacionDestinatario;
use League\Fractal\TransformerAbstract;

class NotificacionDestinatarioREPTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(RepNotificacionDestinatario $model)
    {
        return [
            'id' => $model->getKey(),
            'correo' => $model->correo,
            'nombre' => $model->nombre,
        ];
    }
}
