<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time:  06:20 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\ChatBot;


use App\Models\SEGURIDAD_ERP\ChatBot\Peticion;
use League\Fractal\TransformerAbstract;

class PeticionTransformer extends TransformerAbstract
{

    public function transform(Peticion $model)
    {
        return [
            'id'=>$model->getKey(),
            'respuesta' => $model->getRespuesta(),

        ];
    }
}
