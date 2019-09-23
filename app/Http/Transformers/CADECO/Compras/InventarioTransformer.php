<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 07:20 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\MaterialTransformer;
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
        'material'
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
            'cantidad_format' => $model->cantidad_format,
            'saldo_format' => $model->saldo_format,
            'monto_aplicado' => $model->monto_aplicado
        ];
    }

    /**
     * @param Inventario $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(Inventario $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}