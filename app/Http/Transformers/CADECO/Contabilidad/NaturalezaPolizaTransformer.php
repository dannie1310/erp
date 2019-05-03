<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/03/2019
 * Time: 04:49 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\NaturalezaPoliza;
use League\Fractal\TransformerAbstract;

class NaturalezaPolizaTransformer extends TransformerAbstract
{

    public function transform(NaturalezaPoliza $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}