<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\EmpresaSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\ObraTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use League\Fractal\TransformerAbstract;

class SolicitudRecepcionCFDITransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'empresa',
        'proveedor',
        'cfdi',
        'obra'
    ];

    /**
     * @param SolicitudRecepcionCFDI $model
     * @return array
     */
    public function transform(SolicitudRecepcionCFDI $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'contacto' => $model->contacto,
            'estado' => $model->estado,
            'estado_format' => $model->estado_format,
            'fecha_registro' => $model->fecha_hora_registro_format,
            'observaciones' => $model->comentario,
            'correo_notificaciones' => $model->correo_notificaciones,
        ];
    }

    /**
     * @param SolicitudRecepcionCFDI $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(SolicitudRecepcionCFDI $model)
    {
        if($item = $model->empresa)
        {
            return $this->item($item, new EmpresaSATTransformer);
        }
        return null;
    }

    public function includeProveedor(SolicitudRecepcionCFDI $model)
    {
        if($item = $model->proveedor)
        {
            return $this->item($item, new ProveedorSATTransformer);
        }
        return null;
    }

    public function includeCfdi(SolicitudRecepcionCFDI $model)
    {
        if($item = $model->cfdi)
        {
            return $this->item($item, new CFDSATTransformer);
        }
        return null;
    }

    public function includeObra(SolicitudRecepcionCFDI $model)
    {
        if($item = $model->obra)
        {
            return $this->item($item, new ObraTransformer());
        }
        return null;
    }

}
