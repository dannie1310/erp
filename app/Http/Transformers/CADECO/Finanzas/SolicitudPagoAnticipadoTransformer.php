<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\Compra\OrdenCompraTransformer;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\SolicitudPagoAnticipado;
use League\Fractal\TransformerAbstract;

class SolicitudPagoAnticipadoTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'transaccion_rubro',
        'orden_compra',
        'subcontrato',
        'usuario',
        'empresa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(SolicitudPagoAnticipado $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'antecedente' => $model->id_antecedente,
            'subtotal'=>(float)$model->subtotal,
            'subtotal_format'=>(string) '$ '.number_format(($model->subtotal),2,".",","),
            'impuesto'=>(float)$model->impuesto,
            'impuesto_format'=>(string) '$ '.number_format($model->impuesto,2,".",","),
            'monto'=>(float)$model->monto,
            'total_format'=>(string)$model->monto_format,
            'monto_format'=>(string)$model->monto_format,
            'referencia'=>(string)$model->referencia,
            'retencion'=>(float)$model->retencion,
            'anticipo'=>(float)$model->anticipo,
            'observaciones'=>(string)$model->observaciones,
            'tipo_solicitud'=>(int) $model->tipo_transaccion,
            'fecha_format' => (string)$model->fecha_hora_registro_format,
            'estado' => (int)$model->estado
        ];
    }

    /**
     * @param SolicitudPagoAnticipado $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTransaccionRubro(SolicitudPagoAnticipado $model)
    {
        if ($rubro = $model->transaccion_rubro) {
            return $this->item($rubro, new TransaccionRubroTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudPagoAnticipado $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeOrdenCompra(SolicitudPagoAnticipado $model)
    {
        if ($orden = $model->orden_compra) {
            return $this->item($orden, new OrdenCompraTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudPagoAnticipado $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(SolicitudPagoAnticipado $model)
    {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * Include Empresa
     *
     * @param SolicitudPagoAnticipado $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(SolicitudPagoAnticipado $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}