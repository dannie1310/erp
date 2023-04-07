<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Http\Transformers\SEGURIDAD_ERP\ObraTransformer;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use League\Fractal\TransformerAbstract;

class ProveedorREPTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        "contactos",
        "ubicaciones"
    ];

    protected $defaultIncludes = [

    ];

    public function transform(ProveedorREP $model)
    {
        return [
            'id' => $model->getKey(),
            'rfc_proveedor' => $model->rfc_proveedor,
            'proveedor' => mb_strtoupper($model->proveedor),
            'cantidad_cfdi' => $model->cantidad_cfdi_format,
            'total_cfdi' => $model->total_cfdi_format,
            'total_rep' => $model->total_rep_format,
            'pendiente_rep' => $model->pendiente_rep_format,
            'es_empresa_hermes' => $model->es_empresa_hermes,
            'fecha_ultimo_cfdi_con_ubicacion' => $model->fecha_ultimo_cfdi_con_ubicacion,
            'ultima_ubicacion_sao' => $model->ultima_ubicacion_sao,
            'ultima_ubicacion_contabilidad' => $model->ultima_ubicacion_contabilidad,
            'fecha_ultimo_cfdi_con_ubicacion_format' => $model->fecha_ultimo_cfdi_con_ubicacion_format,
            'fecha_ultima_notificacion' => $model->fecha_ultima_notificacion_format,
            'con_contactos' => $model->con_contactos
        ];
    }

    public function includeContactos(ProveedorREP $model){
        if(count($model->contactos)>0)
        {
            return $this->collection($model->contactos, new ContactoProveedorTransformer);
        }
        return null;
    }

    public function includeUbicaciones(ProveedorREP $model){
        if(count($model->ubicaciones)>0)
        {
            return $this->collection($model->ubicaciones, new ProveedorREPUbicacionTransformer());
        }
        return null;
    }

}
