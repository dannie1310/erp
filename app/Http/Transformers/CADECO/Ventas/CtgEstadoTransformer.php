<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/12/2019
 * Time: 05:29 PM
 */

namespace App\Http\Transformers\CADECO\Ventas;


use App\Models\CADECO\Ventas\CtgEstado;
use League\Fractal\TransformerAbstract;

class CtgEstadoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(CtgEstado $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => $model->descripcion,
            'esatus' => $model->estatus
        ];
    }
}