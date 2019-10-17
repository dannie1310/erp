<?php

/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/08/2019
 * Time: 12:02 PM
 */


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Unidad;
use League\Fractal\TransformerAbstract;

class UnidadTransformer extends TransformerAbstract
{

    public function transform(Unidad $model)
    {
        return [
            'unidad' => $model->unidad,
            'tipo_unidad' => $model->tipo_unidad,
            'descripcion' => $model->descripcion
        ];
    }
}
