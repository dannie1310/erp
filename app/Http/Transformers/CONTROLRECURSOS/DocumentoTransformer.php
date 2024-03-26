<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer;
use App\Models\CONTROL_RECURSOS\Documento;
use League\Fractal\TransformerAbstract;

class DocumentoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cfdi',

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'cfdi',
    ];

    public function transform(Documento $model){
        return [
            'id' => $model->getKey(),
            'folio_format' => $model->folio_con_serie,
            'folio' => $model->FolioDocto,
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'fecha_editar' => $model->fecha_editar,
            'total_format' => $model->total_format,
            'total' => $model->Total,
            'moneda' => $model->moneda_descripcion,
            'serie' => $model->serie_descripcion,
            'id_serie' => $model->IdSerie,
            'id_tipo' => $model->IdTipoDocto,
            'id_proveedor' => $model->IdProveedor,
            'id_empresa' => $model->IdEmpresa,
            'tipo_documento' => $model->tipo_documento,
            'empresa_descripcion' => $model->empresa_descripcion,
            'proveedor_descripcion' => $model->proveedor_descripcion,
            'importe' => $model->Importe,
            'importe_format' => $model->importe_format,
            'retenciones' => $model->Retenciones,
            'retencion_format' => $model->retenciones_format,
            'tasa_iva' => $model->TasaIVA,
            'iva_format' => $model->iva_format,
            'iva' => $model->IVA,
            'tc' => $model->TC,
            'ubicacion' => $model->Ubicacion,
            'departamento' => $model->Departamento,
            'alias_dep' => $model->Alias_Depto,
            'uuid' => $model->uuid,
            'fecha_vencimiento_format' => $model->fecha_vencimiento_format,
            'vencimiento_editar' => $model->vencimiento_editar,
            'vencimiento' => $model->Vencimiento,
            'otros' => $model->OtrosImpuestos,
            'id_moneda' => $model->IdMoneda,
            'estado' => $model->Estatus,
            'estado_descripcion' => $model->estatus_descripcion,
            'estado_color' => $model->color_estado,
            'solicitado' => $model->solicitado,
            'con_segmento' => $model->con_segmento,
            'descuento' => $model->Descuento
        ];
    }
    public function includeCFDI(Documento $model)
    {
        if($item = $model->CFDI)
        {
            return $this->item($item, new CFDSATTransformer);
        }
        return null;
    }
}
