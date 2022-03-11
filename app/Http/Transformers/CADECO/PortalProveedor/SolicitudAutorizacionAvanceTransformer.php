<?php


namespace App\Http\Transformers\CADECO\PortalProveedor;


use App\Models\CADECO\SolicitudAutorizacionAvance;
use League\Fractal\TransformerAbstract;

class SolicitudAutorizacionAvanceTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [];

    public function transform(SolicitudAutorizacionAvance $model)
    {
        return [
            'id' => $model->getKey(),
            'autorizado' => $model->autorizado,
            'id_moneda' => $model->id_moneda,
            'numero_folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'tipo_transaccion' => $model->tipo_transaccion,
            'folio_revision_format' => $model->folio_revision_format,
            'observaciones' => $model->observaciones,
            'impuesto' => (float)$model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'monto_revision' => $model->monto_revision,
            'monto_revision_format' => $model->monto_revision_format,
            'estado' => (int) $model->estado,
            'fecha' => $model->fecha_format,
            'fecha_inicial'=> $model->cumplimiento_format,
            'fecha_final' => $model->vencimiento_format,
            'subtotal' => $model->subtotal,
            'subtotal_format' => $model->subtotal_format,
            'suma_importes' => $model->suma_importes_format,
            'anticipo' => $model->anticipo,
            'anticipo_format' => $model->anticipo_format,
            'monto_anticipo_aplicado' => $model->monto_anticipo_aplicado,
            'monto_anticipo_aplicado_format' => $model->monto_anticipo_aplicado_format,
            'referencia_revision' => $model->referencia_revision,
            'retencion' => $model->retencion,
            'retencion_fondo_garantia' => $model->retencion_fondo_garantia_orden_pago_format,
            'retencion_fondo_garantia_sf' => $model->retencion_fondo_garantia_orden_pago,
            'tipo_cambio' => $model->tipo_cambio,
            'total_retenciones' => $model->suma_retenciones_format,
            'retencion_iva' => $model->IVARetenido,
            'retencion_iva4_format' => $model->retencion_iva4_format,
            'retencion_iva4' => $model->retencion_iva4,
            'retencion_iva6_format' => $model->retencion_iva6_format,
            'retencion_iva6' => $model->retencion_iva6,
            'retencion_iva23' => $model->retencionIVA_2_3,
            'retencion_iva23_format' => $model->retencion_iva23_format,
            'retencion_iva_format' => $model->iva_retenido_format,
            'retencion_iva_porcentaje' => $model->iva_retenido_porcentaje,
            'total_retencion_liberadas' => $model->suma_liberaciones_format,
            'total_deductivas' => $model->suma_deductivas_format,
            'total_deductivas_sf' => $model->suma_deductivas,
            'subtotal_orden_pago' => $model->subtotal_orden_pago_format,
            'iva_orden_pago' => $model->iva_orden_pago_format,
            'iva_orden_pago_revision' => $model->iva_orden_pago_revision_format,
            'total_orden_pago' => $model->total_orden_pago_format,
            'total_anticipo_liberar' => $model->anticipo_a_liberar_format,
            'monto_pagar' => $model->monto_a_pagar,
            'monto_pagar_format' => $model->monto_a_pagar_format,
            'suma_penalizaciones' => $model->suma_penalizaciones_format,
            'suma_penalizaciones_sf' => $model->suma_penalizaciones,
            'suma_penalizaciones_liberadas' => $model->suma_penalizaciones_liberadas_format,
            'suma_penalizaciones_liberadas_sf' => $model->suma_penalizaciones_liberadas,
            'seleccionado' => false,
            'consecutivo' => $model->estimacion_folio_consecutivo_format,
            'tc_actualizado' => false,
        ];
    }
}
