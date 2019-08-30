<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Movimiento;
use League\Fractal\TransformerAbstract;

class MovimientoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     * @var array
     */
    protected $availableIncludes = [
        'inventario'
    ];

    public function transform(Movimiento $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'cantidad' => $model->cantidad,
            'monto_total' => $model->monto_total,
            'monto_pagado' => $model->monto_pagado,
            'cantidad_format' => $model->cantidad_format,
        ];
    }

    public function includeInventario(Movimiento $model)
    {
        if($inventario = $model->inventario)
        {
            return $this->item($inventario, new InventarioTransformer);
        }
        return null;
    }

}