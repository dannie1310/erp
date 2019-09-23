<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:29 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\NuevoLotePartida;
use League\Fractal\TransformerAbstract;

class NuevoLotePartidaTransformer extends TransformerAbstract
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

    public function transform(NuevoLotePartida $model)
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
     * @param NuevoLotePartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(NuevoLotePartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}