<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:33 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use League\Fractal\TransformerAbstract;

class TipoCuentaGeneralTransformer extends TransformerAbstract
{
    public function transform(TipoCuentaContable $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}