<?php


namespace App\Http\Transformers\CTPQ;

use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;

class PolizaPosiblesCFDITransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     * @var array
     */
    protected $availableIncludes = [
    ];

    public function transform(CFDSAT $model)
    {
        return [
            'conceptos_txt' => $model->conceptos_txt,
            'fecha_cfdi' => $model->fecha_cfdi,
            'folio' => $model->folio,
            'grado_coincidencia' => $model->grado_coincidencia,
            'id' => $model->id,
            'id_proveedor_sat' => $model->id_proveedor_sat,
            'importe_iva' => $model->importe_iva,
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'seleccionado' => $model->seleccionado,
            'serie' => $model->serie,
            'tipo_comprobante' => $model->tipo_comprobante,
            'total' => $model->total,
            'uuid' => $model->uuid,
        ];
    }
}
