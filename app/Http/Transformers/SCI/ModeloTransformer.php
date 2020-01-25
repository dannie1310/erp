<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 08:21 p. m.
 */


namespace App\Http\Transformers\SCI;


use App\Models\SCI\Modelo;
use League\Fractal\TransformerAbstract;

class ModeloTransformer extends TransformerAbstract
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

    public function transform(Modelo $model)
    {
        return [
            'id' => $model->getKey(),
            'modelo' => $model->modelo,
        ];
    }


}
