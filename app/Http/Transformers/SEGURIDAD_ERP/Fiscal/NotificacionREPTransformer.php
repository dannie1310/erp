<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacion;
use League\Fractal\TransformerAbstract;

class NotificacionREPTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
       
    ];

    protected $defaultIncludes = [

    ];

    public function transform(RepNotificacion $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_format,
            'envia' => $model->u_registro,
            'cantidad_cfdi_format' => $model->cantidad_cfdi_format,
            'monto_cfdi_format' => $model->monto_format,
            'cfdi_atendidos_format' => $model->cfdi_atendidos_format,
            'cfdi_nuevos_format' => $model->cfdi_nuevos_format,
            'cfdi_cancelados_format' => $model->cfdi_cancelados_format
        ];
    }
}
