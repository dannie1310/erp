<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/08/2020
 * Time: 04:41 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgGiro;
use League\Fractal\TransformerAbstract;

class GiroTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(CtgGiro $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
        ];
    }

}