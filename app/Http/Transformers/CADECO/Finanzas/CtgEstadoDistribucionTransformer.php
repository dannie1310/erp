<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/06/2019
 * Time: 05:23 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgEstadoDistribucionRecursoRemesa;
use League\Fractal\TransformerAbstract;

class CtgEstadoDistribucionTransformer extends TransformerAbstract
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

    public function transform(CtgEstadoDistribucionRecursoRemesa $model){
        return [
            'id' => $model->getKey(),
            'estado' => (int) $model->estado,
            'descripcion' => (string) $model->descripcion
        ];
    }
}