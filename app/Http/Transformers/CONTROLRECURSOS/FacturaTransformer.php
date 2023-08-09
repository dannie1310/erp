<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Factura;
use League\Fractal\TransformerAbstract;

class FacturaTransformer extends TransformerAbstract
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

    public function transform(Factura $model){
        return [
            'id' => $model->getKey(),
            'folio_format' => $model->folio_con_serie,
            'folio' => $model->FolioDocto,
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'total_format' => $model->total_format,
            'moneda' => $model->moneda_descripcion,
            'serie' => $model->serie_descripcion,
            'id_serie' => $model->IdSerie,
            'id_tipo' => $model->IdTipoDocto,
            'id_proveedor' => $model->IdProveedor,
            'tipo_documento' => $model->tipo_documento,
            'empresa_descripcion' => $model->empresa_descripcion,
            'proveedor_descripcion' => $model->proveedor_descripcion,
            'importe_format' => $model->importe_format,
            'retencion_format' => $model->importe_format,
            'tasa_iva' => $model->TasaIVA,
            'iva_format' => $model->iva_format,
            'tc' => $model->TC,
            'ubicacion' => $model->Ubicacion,
            'departamento' => $model->Departamento,
            'alias_dep' => $model->Alias_Depto,
            'uuid' => $model->uuid,
            'fecha_vencimiento_format' => $model->fecha_vencimiento_format,
            'vencimiento_editar' => $model->vencimiento_editar,
            'vencimiento' => $model->Vencimiento,
            'otros' => $model->OtrosImpuestos
        ];
    }
}
