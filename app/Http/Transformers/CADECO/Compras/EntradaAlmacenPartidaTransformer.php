<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 04:42 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Item;
use League\Fractal\TransformerAbstract;

class EntradaAlmacenPartidaTransformer extends TransformerAbstract
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

    public function transform(Item $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha_format' => (string)$model->fecha_format,
            'numero_folio_format' => (string)$model->numero_folio_format,
            'subtotal' => (float)$model->subtotal,
            'subtotal_format' => (string) '$ '.number_format(($model->subtotal),2,".",","),
            'impuesto' => (float)$model->impuesto,
            'impuesto_format' => (string) '$ '.number_format($model->impuesto,2,".",","),
            'monto' => (float)$model->monto,
            'total_format' => (string)$model->monto_format,
            'monto_format' => (string)$model->monto_format,
            'referencia' => (string)$model->referencia,
            'retencion' => (float)$model->retencion,
            'anticipo' => (float)$model->anticipo,
            'observaciones' => (string)$model->observaciones,
            'observaciones_format' => (string)$model->observaciones_format,
            'id_moneda' => (int)$model->id_moneda,
            'destino '=> (string)$model->destino,
            'saldo' => (float)$model->saldo,
            'tipo_nombre' => (string)$model->getNombre(),
            'dato_transaccion' => (string)$model->getEncabezadoReferencia(),
            'monto_facturado_oc' => (float) $model->montoFacturadoOrdenCompra,
            'monto_facturado_ea' => (float) $model->montoFacturadoEntradaAlmacen,
            'monto_solicitado' => (float) $model->montoPagoAnticipado
        ];
    }
}