<?php


namespace App\Http\Transformers\CADECO\Finanzas;

use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\SolicitudTransformer;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use League\Fractal\TransformerAbstract;

class LayoutPagoPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */

    protected $availableIncludes = [
        'solicitud',
        'factura',
        'moneda'
    ];
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(LayoutPagoPartida $model){

        return [
            'id' => $model->getKey(),
            'fecha_pago' => date('Y-m-d', strtotime($model->fecha_pago)),
            'fecha_pago_format' => date('d/m/Y', strtotime($model->fecha_pago)),
            'id_layout_pagos' => $model->id_layout_pagos,
            'id_transaccion' => $model->id_transaccion,
            'id_refente'=>$model->id_referente,
            'monto_transaccion' => $model->monto_transaccion,
            'monto_transaccion_format' => '$ ' . number_format($model->monto_transaccion,2,".",","),
            'monto_transaccion_format_2' =>  number_format($model->monto_transaccion,2,".",","),
            'id_moneda' => $model->id_moneda,
            'tipo_cambio' => $model->tipo_cambio_format,
            'cuenta_cargo' => $model->cuenta->numero,
            'id_cuenta_cargo' => $model->id_cuenta_cargo,
            'monto_pagado' => $model->monto_pagado,
            'monto_pagado_format' => '$ ' . number_format($model->monto_pagado,2),
            'referencia_pago' => $model->referencia_pago,
            'id_documento_remesa' => $model->id_documento_remesa,
            'id_transaccion_pago' => $model->id_transaccion_pago,
            'referencia' => $model->documento_pagable->referencia_pagable,
            'fecha_format' => $model->documento_pagable->fecha_format,
            'vencimiento_format' => $model->documento_pagable->vencimiento_format,
            'saldo_format'=>(string) '$ '.number_format(($model->documento_pagable->saldo_pagable),2,".",","),
            'beneficiario'=>$model->documento_pagable->beneficiario,
            'folio_pago_format'=>$model->folio_pago_format,
            'clase_badge_estado'=>($model->pago)?"badge badge-success":"badge badge-primary",
            'estado'=>($model->pago)?"Pagado":"Por Autorizar",
        ];
    }

    /**
     * @param LayoutPagoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeFactura(LayoutPagoPartida $model)
    {
        if($factura = $model->factura){
            return $this->item($factura, new FacturaTransformer);
        }
        return null;
    }

    /**
     * @param LayoutPagoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(LayoutPagoPartida $model)
    {
        if($solicitud_pago_anticipado = $model->solicitud){
            return $this->item($solicitud_pago_anticipado, new SolicitudTransformer);
        }
        return null;
    }

    /**
     * @param LayoutPagoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(LayoutPagoPartida $model)
    {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}
