<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 12:00 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgPlaza;
use League\Fractal\TransformerAbstract;

class CtgPlazaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(CtgPlaza $model)
    {
        return [
            'id' => $model->getKey(),
            'clave' => $model->clave,
            'clave_format' => $model->clave_format,
            'nombre' => $model->nombre
        ];
    }
}