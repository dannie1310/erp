<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 13/03/2020
 * Time: 03:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use App\Models\CADECO\Concepto;
use League\Fractal\TransformerAbstract;

class TarjetaTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(Concepto $model) {
        return [
            'id' => (int) $model->getKey(),
            'nivel'=> $model->nivel,
            'descripcion'=> $model->descripcion,
            'unidad'=> $model->unidad,
            'cantidad_presupuestada'=> $model->cantidad_presupuestada,
            'cantidad_presupuestada_format'=> number_format($model->cantidad_presupuestada, 2, '.', ','),
            'monto_presupuestado'=> $model->monto_presupuestado,
            'monto_presupuestado_format'=> '$ '. number_format($model->monto_presupuestado, 2, '.', ','),
            'precio_unitario'=> $model->precio_unitario,
            'precio_unitario_format'=> '$ '.number_format($model->precio_unitario, 2, '.', ','),
        ];
    }
}