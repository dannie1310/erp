<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 10:59 AM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Moneda;
use League\Fractal\TransformerAbstract;

class MonedaTransformer extends TransformerAbstract
{
    /**
     * @param Moneda $model
     * @return array
     */
    public function transform(Moneda $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'nombre'=>(string)$model->nombre,
            'abreviatura'=>(string)$model->abreviatura,
        ];
    }

}