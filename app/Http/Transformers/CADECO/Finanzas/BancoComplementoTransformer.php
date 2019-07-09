<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 12:41 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\BancoComplemento;
use League\Fractal\TransformerAbstract;

class BancoComplementoTransformer extends TransformerAbstract
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

    public function transform(BancoComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre_corto' => $model->nombre_corto
        ];
    }

}