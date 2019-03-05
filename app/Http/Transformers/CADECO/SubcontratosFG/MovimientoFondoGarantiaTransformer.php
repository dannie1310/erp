<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 04/03/2019
 * Time: 06:12 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\MovimientoFondoGarantia;
use League\Fractal\TransformerAbstract;

class MovimientoFondoGarantiaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo_movimiento',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo_movimiento',
    ];
    /**
     * @param MovimientoFondoGarantia $model
     * @return array
     */
    public function transform(MovimientoFondoGarantia $model)
    {
        return [
            'importe' => (float) $model->importe,
            'importe_format' => (string) '$ ' . number_format($model->importe,2,".",","),
            'importe_abs_format' => (string) '$ ' . number_format(abs($model->importe),2,".",","),
            'saldo' => (float)$model->saldo,
            'saldo_format' => (string) '$ ' . number_format($model->saldo,2,".",","),
            'observaciones' => (string)$model->observaciones,
            'usuario_registra_desc'=>(string)$model->usuario_registra_desc,
            'usuario_completo_registra_desc'=>(string)$model->usuario_completo_registra_desc,
            'created_at_format'=>(string)date_format(date_create($model->created_at),"d/m/Y h:i:s"),
        ];
    }
    /**
     * Include Tipo de Movimiento
     *
     * @param MovimientoFondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipoMovimiento(MovimientoFondoGarantia $model) {
        if ($tipo_movimiento = $model->tipo) {
            return $this->item($tipo_movimiento, new CtgTipoMovimientoFondoGarantiaTransformer);
        }
        return null;
    }
}