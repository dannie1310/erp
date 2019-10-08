<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:04 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Compras\InventarioTransformer;
use App\Models\CADECO\Almacen;
use League\Fractal\TransformerAbstract;

class AlmacenTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'inventario',

    ];


    public function transform(Almacen $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->descripcion,
            'tipo' => (string) $model->tipo
        ];
    }

}
