<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/2019
 * Time: 7:33 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Sucursal;
use League\Fractal\TransformerAbstract;

class SucursalTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];


    public function transform(Sucursal $model)
    {
        return [
            'id'=>$model->getKey(),
            'descripcion'=>$model->descripcion,
            'direccion'=>$model->direccion,

        ];

    }

}
