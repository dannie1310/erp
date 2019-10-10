<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\DocumentoTransformer;
use App\Models\CADECO\Factura;
use League\Fractal\TransformerAbstract;

class FacturaTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
       'documento',
        'moneda'
    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Factura $model)
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
            'fecha_format' => (string)$model->fecha_format,
            'estado' => (int)$model->estado,
            'cumplimiento' => (string)$model->cumplimiento_form,
            'vencimiento' => $model->vencimiento_form,
            'tipo_cambio' => $model->tipo_cambio,
            'a_pagar' => $model->autorizado
        ];
    }

    public function includeDocumento(Factura $model)
    {
        if ($documento = $model->documento) {
            return $this->item($documento, new DocumentoTransformer);
        }
        return null;
    }

    public function includeMoneda(Factura $model)
    {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    public function includeEmpresa(Factura $model){

    }
}
