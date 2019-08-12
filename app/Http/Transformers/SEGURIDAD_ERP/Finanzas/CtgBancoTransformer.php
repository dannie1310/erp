<?php

/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/08/2019
 * Time: 12:02 PM
 */


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgBanco;
use League\Fractal\TransformerAbstract;

class CtgBancoTransformer extends TransformerAbstract
{

    public function transform(CtgBanco $model)
    {
        return [
          'id'=> $model->getKey(),
          'razon_social'=> $model->razon_social,
          'nombre_corto'=>$model->nombre_corto,
          'descripcion_corta'=>$model->descripcion_corta,
          'clave'=>str_pad($model->clave, 3, '0', STR_PAD_LEFT),
          'clave_h2h'=>$model->clave_h2h,
          'clave_format' => $model->clave_format
        ];
    }
}
