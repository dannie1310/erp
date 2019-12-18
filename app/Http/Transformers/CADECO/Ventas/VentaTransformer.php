<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:17 PM
 */

namespace App\Http\Transformers\CADECO\Ventas;


use App\Models\CADECO\Venta;
use League\Fractal\TransformerAbstract;

class VentaTransformer extends TransformerAbstract
{
    public function transform(Venta $model) {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'referencia' => (string) $model->referencia,
            'observaciones' => (string) $model->observaciones,
            'estado' => (int)$model->estado,
            'estado_format' => $model->estado_format,
            'folio' => $model->numero_folio,
            'opciones' => $model->opciones,
            'operacion' => $model->operacion,
            'folio_format' => $model->numero_folio_format,
        ];
    }
}
