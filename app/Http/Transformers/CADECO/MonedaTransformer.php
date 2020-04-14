<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 10:59 AM
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 05:42 PM
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
            'tipo' => $model->tipo,
            'abreviatura'=>(string)$model->abreviatura,
            'tipo_cambio' => $model->tipo_cambio,
        ];
    }
}
