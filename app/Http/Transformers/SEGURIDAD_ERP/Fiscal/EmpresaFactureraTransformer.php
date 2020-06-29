<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 24/06/2020
 * Time: 01:56 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\EmpresaFacturera as Model;
use League\Fractal\TransformerAbstract;

class EmpresaFactureraTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'poliza'
    ];

    public function transform(Model $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'palabras_clave' => $model->palabras_clave,
        ];
    }

}