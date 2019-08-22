<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\SalidaAlmacenPartida;
use League\Fractal\TransformerAbstract;

class SalidaAlmacenPartidasTransformer extends TransformerAbstract
{
    /**
 * List of resources possible to include
 * @var array
 */
    protected $availableIncludes = [
        'inventario',
        'movimiento',
        'almacen',
        'material'
    ];

    public function transform(SalidaAlmacenPartida $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'unidad' => $model->unidad,
            'cantidad' => $model->cantidad,
            'cantidad_material' => $model->cantidad_material,
            'cantidad_format' => $model->cantidad_format,
            'saldo' => $model->saldo
        ];
    }

    /**
     * Include Almacen
     * @param SalidaAlmacenPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(SalidaAlmacenPartida $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    public function includeInventario(SalidaAlmacenPartida $model)
    {
        if($inventario = $model->inventario)
        {
            return $this->item($inventario, new InventarioTransformer);
        }
        return null;
    }

    /**
     * Include Material
     * @param SalidaAlmacenPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(SalidaAlmacenPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    public function includeMovimiento(SalidaAlmacenPartida $model)
    {
        if($movimiento = $model->movimiento)
        {
            return $this->item($movimiento, new MovimientoTransformer);
        }
        return null;
    }
}