<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Http\Transformers\CTPQ\AsocCFDITransformer;
use App\Http\Transformers\CTPQ\PolizaPosiblesCFDITransformer;
use App\Models\CTPQ\Poliza;
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
        'factura',
        'posibles_cfdi'
    ];
    /**
     * @var string[]
     */
    protected $defaultIncludes = [
        'factura',
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
            'fecha_factura' => $model->fecha_factura_format,
            'importe_factura' => $model->importe_factura_format,
            'moneda_factura' => $model->moneda_factura,
            'tc_factura'=>$model->tc_factura_format,
            'importe_mxn'=>$model->importe_mxn_format,
            'saldo'=>$model->saldo_format,
            'tc_saldo'=>$model->tc_saldo_format,
            'saldo_mxn'=>$model->saldo_mxn_format,
            'uuid'=>$model->uuid,
            'coincide_rfc_empresa'=>$model->coincide_rfc_empresa ==1 ?true:false,
            'coincide_rfc_proveedor'=>$model->coincide_rfc_proveedor ==1 ?true:false,
            'coincide_folio'=>$model->coincide_folio ==1 ?true:false,
            'coincide_fecha'=>$model->coincide_fecha ==1 ?true:false,
            'coincide_importe'=>$model->coincide_importe ==1 ?true:false,
            'coincide_moneda'=>$model->coincide_moneda ==1 ?true:false,
            'coincide_tipo_cambio'=>$model->coincide_tipo_cambio ==1 ?true:false,
            'inconsistencia_saldo'=>$model->inconsistencia_saldo ==1 ?true:false,
            'es_moneda_nacional'=>$model->es_moneda_nacional ==1 ?true:false,
            'id_caso_sin_cfdi'=>$model->id_caso_sin_cfdi,
            'caso_sin_cfdi' => $model->caso_sin_cfdi_txt,
        ];
    }
    public function includeFactura(LayoutPasivoPartida $partida)
    {
        if($item = $partida->cfdi)
        {
            return $this->item($item, new CFDSATTransformer);
        }
        return null;
    }

    public function includePosiblesCFDI(Poliza $poliza)
    {
        if($items = $poliza->posibles_cfdi)
        {
            return $this->collection($items, new PolizaPosiblesCFDITransformer);
        }
        return null;
    }
}
