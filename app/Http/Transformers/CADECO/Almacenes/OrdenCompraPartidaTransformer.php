<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/10/2019
 * Time: 04:16 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\EntregaTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\OrdenCompraPartida;
use League\Fractal\TransformerAbstract;

class OrdenCompraPartidaTransformer extends TransformerAbstract
{

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
            'cantidad' => $model->cantidad,
            'cantidad_material' => $model->cantidad_material,
            'unidad' => $model->unidad,
            'material' => $model->material->descripcion,
        ];
    }
}