<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use League\Fractal\TransformerAbstract;

class LayoutPasivoPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * @param LayoutPasivoPartida $model
     * @return array
     */
    public function transform(LayoutPasivoPartida $model) {
        return [
            'id' => (int) $model->id,
            'obra' => $model->obra,
            'bbdd_contpaq' => $model->bbdd_contpaq,
            'rfc_empresa' => $model->rfc_empresa,
            'empresa' => $model->empresa,
            'rfc_proveedor' => $model->rfc_proveedor,
            'proveedor' => $model->proveedor,
            'concepto' => $model->concepto,
            'folio_factura' => $model->folio_factura,
            'fecha_factura' => $model->fecha_factura,
            'importe_factura' => $model->importe_factura,
            'moneda_factura' => $model->moneda_factura,
            'tc_factura'=>$model->tc_factura,
            'importe_mxn'=>$model->importe_mxn,
            'saldo'=>$model->saldo,
            'uuid'=>$model->uuid,
        ];
    }
}
