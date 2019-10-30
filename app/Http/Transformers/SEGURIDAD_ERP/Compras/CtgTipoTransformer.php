<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time:  06:20 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgTipo;
use League\Fractal\TransformerAbstract;

class CtgTipoTransformer extends TransformerAbstract
{

    public function transform(CtgTipo $model)
    {
        return [
            'id'=>$model->getKey(),
            'descripcion' => $model->descripcion,
            'descripcion_corta' => $model->descripcion_corta

        ];

    }
}
