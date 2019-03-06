<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/02/2019
 * Time: 11:07 AM
 */

namespace App\Http\Transformers\CADECO\Subcontratos;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Models\CADECO\Subcontrato;
use League\Fractal\TransformerAbstract;

class SubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'moneda'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        /*'empresa'*/
    ];

    /**
     * @param Subcontrato $model
     * @return array
     */
    public function transform(Subcontrato $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha_format' => (string)$model->fecha_format,
            'numero_folio_format'=>(string)$model->numero_folio_format,
            'subtotal'=>(float)$model->monto-$model->impuesto,
            'subtotal_format'=>(string) '$ '.number_format(($model->monto-$model->impuesto),2,".",","),
            'impuesto'=>(float)$model->impuesto,
            'impuesto_format'=>(string) '$ '.number_format($model->impuesto,2,".",","),
            'monto'=>(float)$model->monto,
            'total_format'=>(string)$model->monto_format,
            'monto_format'=>(string)$model->monto_format,
            'referencia'=>(string)$model->referencia,
            'retencion'=>(float)$model->retencion,
            'anticipo'=>(float)$model->anticipo,
            'observaciones'=>(string)$model->observaciones,
        ];
    }
    /**
     * Include Empresa
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(Subcontrato $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * Include Moneda
     *
     * @param Subcontrato $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeMoneda(Subcontrato $model) {
        if ($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}