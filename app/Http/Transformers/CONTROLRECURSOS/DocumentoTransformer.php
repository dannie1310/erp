<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

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

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

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
            'id_moneda' => $model->IdMoneda
        ];
    }
}
