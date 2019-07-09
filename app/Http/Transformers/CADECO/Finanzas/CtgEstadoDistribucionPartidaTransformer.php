<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 06:22 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CtgEstadoDistribucionRecursoRemesaPartida;
use League\Fractal\TransformerAbstract;

class CtgEstadoDistribucionPartidaTransformer extends TransformerAbstract
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

    public function transform(CtgEstadoDistribucionRecursoRemesaPartida $model){
        return [
            'id' => $model->getKey(),
            'estado' => $model->estado,
            'descripcion' => $model->descripcion
        ];
    }
}