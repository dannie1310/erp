<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/10/2019
 * Time: 04:16 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\EntregaTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\OrdenCompraPartida;
use League\Fractal\TransformerAbstract;

class OrdenCompraPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'material',
        'entrega',
        'complemento'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];


    public function transform(OrdenCompraPartida $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion'=> $model->id_transaccion,
            'id_antecedente' => $model->id_antecedente,
            'item_antecedente' => $model->item_antecedente,
            'cantidad' => $model->cantidad,
            'cantidad_material' => $model->cantidad_material,
            'precio_unitario' => $model->precio_unitario,
            'estado'=> $model->estado,
            'unidad' => $model->unidad,
            'importe' => $model->importe,
            'precio_unitario' => $model->precio_unitario,
            'precio_material' => $model->precio_material,
            'numero' => $model->numero
        ];
    }

    /**
     * @param OrdenCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(OrdenCompraPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEntrega(OrdenCompraPartida $model)
    {
        if($entrega = $model->entrega)
        {
            return $this->item($entrega, new EntregaTransformer);
        }
        return null;
    }

    /**
     * @param OrdenCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeComplemento(OrdenCompraPartida $model)
    {
        dd($model->orden_partida_complemento);
        if($complemento = $model->orden_partida_complemento)
        {
            return $this->item($complemento, new OrdenCompraPartidaComplementoTransformer);
        }
        return null;
    }
}