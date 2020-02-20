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
    protected $availableIncludes = [
        'movimientos_poliza',
    ];

    public function transform(Poliza $model) {
        return [
            'id' => (int) $model->getKey(),
            'concepto' => (string) $model->Concepto,
            'folio' => (string) $model->Folio,
            'ejercicio' => (string) $model->Ejercicio,
            'periodo' => (string) $model->Periodo,
            'fecha' => (string) $model->fecha_format,
            'cargos' => (string) $model->cargos_format,
            'abonos' => (float) $model->Abonos,
            'tipo' => (string) $model->tipo_poliza->Nombre
        ];
    }

    /**
     * Include Movimienos
     * @param Poliza $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMovimientosPoliza(Poliza $model){
        if ($movimientos = $model->movimientos()->orderBy("TipoMovto")->get()) {
            return $this->collection($movimientos, new PolizaMovimientoTransformer());
        }
        return null;
    }
}