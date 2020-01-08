<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/01/2020
 * Time: 01:38 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use League\Fractal\TransformerAbstract;

class EfoTransformer extends TransformerAbstract
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
        'ctg_estado'
    ];

    public function transform(CtgEfos $model)
    {
        return[
            'id' => $model->getKey(),
            'rfc' => $model->rfc,
            'estado' => $model->estado
        ];
    }

    public function includeCtgEstado(CtgEfos $model)
    {
        if($estado = $model->ctgEstado)
        {
            return $this->item($estado, new CtgEstadoEfosTransformer);
        }
        return null;
    }
}