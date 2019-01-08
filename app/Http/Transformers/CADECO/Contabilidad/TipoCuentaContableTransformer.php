<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 08:36 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use League\Fractal\TransformerAbstract;

class TipoCuentaContableTransformer extends TransformerAbstract
{
    public function transform(TipoCuentaContable $model) {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}