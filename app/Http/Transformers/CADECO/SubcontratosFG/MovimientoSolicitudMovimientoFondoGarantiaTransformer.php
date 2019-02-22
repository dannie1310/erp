<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 22/02/2019
 * Time: 03:39 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantia;
use League\Fractal\TransformerAbstract;

class MovimientoSolicitudMovimientoFondoGarantiaTransformer extends TransformerAbstract
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
     * @param MovimientoSolicitudMovimientoFondoGarantia $model
     * @return array
     */
    public function transform(MovimientoSolicitudMovimientoFondoGarantia $model)
    {
        return [
            'observaciones' => (string)$model->observaciones,
            'usuario_registra'=>(string)$model->usuario_registra,
            'usuario_completo_registra_desc'=>(string)$model->usuario_completo_registra_desc,
            'created_at_format'=>(string)$model->created_at_format,
        ];
    }
    /**
     * Include Tipo de Movimiento
     *
     * @param MovimientoSolicitudMovimientoFondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipoMovimiento(MovimientoSolicitudMovimientoFondoGarantia $model) {
        if ($tipo_movimiento = $model->tipo) {
            return $this->item($tipo_movimiento, new CtgTipoMovimientoSolicitudTransformer);
        }
        return null;
    }

}