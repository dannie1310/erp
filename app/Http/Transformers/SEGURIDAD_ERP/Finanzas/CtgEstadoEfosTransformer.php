<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/01/2020
 * Time: 01:41 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use League\Fractal\TransformerAbstract;

class CtgEstadoEfosTransformer extends TransformerAbstract
{
    public function transform(CtgEstadosEfos $model)
    {
        return[
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}