<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 07:20 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Inventario;
use League\Fractal\TransformerAbstract;

class InventarioTransformer extends TransformerAbstract
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

    public function transform(Inventario $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'cantidad' => $model->cantidad,
            'saldo' => $model->saldo,
            'monto_total' => $model->monto_total,
            'monto_pagado' => $model->monto_pagado,
            'monto_aplicado' => $model->monto_aplicado
        ];
    }
}