<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\Contabilidad\CuentaMaterialTransformer;
use App\Models\CADECO\Material;
use League\Fractal\TransformerAbstract;

class MaterialAjustesTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    public function transform(Material $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'numero_parte' => $model->numero_parte,
            'unidad' => $model->unidad,
            'saldo_almacen' => $model->saldo_almacen,
            'cantidad_almacen' => $model->cantidad_almacen,
        ];
    }
}
