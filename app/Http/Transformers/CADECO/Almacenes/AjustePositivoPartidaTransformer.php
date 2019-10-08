<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 07:36 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\Compras\InventarioTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\AjustePositivoPartida;
use League\Fractal\TransformerAbstract;

class AjustePositivoPartidaTransformer extends TransformerAbstract
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

    public function transform(AjustePositivoPartida $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion'=> $model->id_transacccion,
            'item_antecedente' => $model->item_antecedente,
            'cantidad' => $model->cantidad,
            'saldo' => $model->saldo,
            'importe' => $model->importe,
            'precio_unitario' => $model->precio_unitario,
            'estado'=> $model->estado,
            'tipo' => $model->tipo,
            ];
    }

    /**
     * @param AjustePositivoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(AjustePositivoPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * @param AjustePositivoPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeInventario(AjustePositivoPartida $model)
    {
        if($inventario = $model->inventario)
        {
            return $this->item($inventario, new InventarioTransformer);
        }
        return null;
    }
}