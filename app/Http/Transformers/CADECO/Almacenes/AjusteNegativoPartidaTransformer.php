<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 12:51 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\Compras\InventarioTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\AjusteNegativoPartida;
use League\Fractal\TransformerAbstract;

class AjusteNegativoPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
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

    public function transform(AjusteNegativoPartida $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion'=> $model->id_transacccion,
            'item_antecedente' => $model->item_antecedente,
            'cantidad' => $model->cantidad,
            'saldo' => $model->saldo,
            'importe' => $model->importe,
            'precio_unitario' => $model->precio_unitario,
            'estado'=> $model->estado
        ];
    }

    /**
     * @param AjusteNegativoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(AjusteNegativoPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * @param AjusteNegativoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeInventario(AjusteNegativoPartida $model)
    {
        if($inventario = $model->inventario)
        {
            return $this->item($inventario, new InventarioTransformer);
        }
        return null;
    }
}