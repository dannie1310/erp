<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 02:03 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Cambio;
use League\Fractal\TransformerAbstract;

class CambioTransformer extends TransformerAbstract
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

    public function transform(Cambio $model){
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha,
            'cambio' => $model->cambio
        ];
    }
}