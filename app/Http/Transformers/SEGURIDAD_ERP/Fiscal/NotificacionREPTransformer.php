<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacion;
use League\Fractal\TransformerAbstract;

class NotificacionREPTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'destinatarios',
        'proveedor',
        'proveedor_rep'
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
            'cfdi_cancelados_format' => $model->cfdi_cancelados_format,
            'cuerpo_correo' => $model->cuerpo_correo
        ];
    }

    /**
     * @param RepNotificacion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDestinatarios(RepNotificacion $model)
    {
        if($destinatarios = $model->destinatarios)
        {
            return $this->collection($destinatarios, new NotificacionDestinatarioREPTransformer);
        }
        return null;
    }

    /**
     * @param RepNotificacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(RepNotificacion $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorSATTransformer);
        }
        return null;
    }

    /**
     * @param RepNotificacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedorRep(RepNotificacion $model)
    {
        if($proveedor_rep = $model->proveedor_rep)
        {
            return $this->item($proveedor_rep, new ProveedorREPTransformer);
        }
        return null;
    }
}
