<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaMaterial;
use League\Fractal\TransformerAbstract;

class TipoCuentaMaterialTransformer extends TransformerAbstract
{

    public function transform(TipoCuentaMaterial $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}