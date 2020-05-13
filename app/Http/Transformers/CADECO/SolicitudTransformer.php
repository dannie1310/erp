<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Solicitud;
use League\Fractal\TransformerAbstract;

class SolicitudTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'fondo',
        'empresa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Solicitud $model)
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
            'saldo_format'=>(string)$model->monto_format,
            'referencia'=>(string)'S/P '.$model->numero_folio_format,
            'retencion'=>(float)$model->retencion,
            'anticipo'=>(float)$model->anticipo,
            'observaciones'=>(string)$model->observaciones,
            'tipo_solicitud'=>(int) $model->tipo_transaccion,
            'fecha_format' => (string)$model->fecha_format,
            'estado' => (int)$model->estado,
            'cumplimiento' => (string)$model->cumplimiento_form,
            'vencimiento' => $model->vencimiento_form,
            'vencimiento_format' => $model->vencimiento_format,
            'moneda' => $model->moneda->nombre
        ];
    }
    /**
     * @param Solicitud $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeFondo(Solicitud $model)
    {
        if($fondo = $model->fondo){
            return $this->item($fondo, new FondoTransformer);
        }
        return null;
    }

    /**
     * @param Solicitud $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Solicitud $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}
