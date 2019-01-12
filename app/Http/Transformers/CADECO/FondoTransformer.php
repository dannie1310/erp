<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 05:44 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Fondo;
use League\Fractal\TransformerAbstract;

class FondoTransformer extends TransformerAbstract
{
    public function transform(Fondo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'saldo' => $model->saldo
        ];
    }
}