<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:57 AM
 */

namespace App\Http\Transformers\CTPQ;


use App\Models\CTPQ\Poliza;
use League\Fractal\TransformerAbstract;

class PolizaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'movimientos_poliza',
    ];

    public function transform(Poliza $model) {
        return [
            'id' => (int) $model->getKey(),
            'concepto' => (string) $model->Concepto,
            'fecha' => (string) $model->Fecha,
            'cargos' => (float) $model->Cargos,
            'abonos' => (float) $model->Abonos
        ];
    }

    /**
     * Include Movimienos
     * @param Poliza $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMovimientos(Poliza $model){
        if ($movimientos = $model->movimientos) {
            return $this->collection($movimientos, new PolizaMovimientoTransformer());
        }
        return null;
    }
}