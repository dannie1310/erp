<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 12:12 PM
 */

namespace App\Http\Transformers\CTPQ;


use League\Fractal\TransformerAbstract;

class PolizaMovimientoTransformer extends TransformerAbstract
{
    public function transform(Poliza $model) {
        return [
            'id' => (int) $model->getKey(),
            'concepto' => (string) $model->Concepto,
            'referencia' => (string) $model->Referencia,
            'fecha' => (string) $model->Fecha,
            'cargos' => (float) $model->Cargos,
            'abonos' => (float) $model->Abonos
        ];
    }
}