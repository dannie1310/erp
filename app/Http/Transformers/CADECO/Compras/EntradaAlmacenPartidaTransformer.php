<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 04:42 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\EntradaMaterialPartida;
use League\Fractal\TransformerAbstract;

class EntradaAlmacenPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'almacen',
        'material',
        'inventario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(EntradaMaterialPartida $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'unidad' => $model->unidad,
            'cantidad' => $model->cantidad,
            'cantidad_material' => $model->cantidad_material,
            'saldo' => $model->saldo
        ];
    }

    /**
     * Include Almacen
     * @param EntradaMaterialPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(EntradaMaterialPartida $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    /**
     * Include Material
     * @param EntradaMaterialPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(EntradaMaterialPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * Include Inventario
     * @param EntradaMaterialPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeInventario(EntradaMaterialPartida $model)
    {
        if($inventario = $model->inventario)
        {
            return $this->collection($inventario, new InventarioTransformer);
        }
        return null;
    }
}