<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 05:42 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Moneda;
use League\Fractal\TransformerAbstract;

class MonedaTransformer extends TransformerAbstract
{
    public function transform(Moneda $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre' => $model->nombre,
            'tipo' => $model->tipo,
            'abreviatura' => $model->abreviatura
        ];
    }
}