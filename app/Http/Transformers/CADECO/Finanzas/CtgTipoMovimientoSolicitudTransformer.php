<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 09:08 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use League\Fractal\TransformerAbstract;

class CtgTipoMovimientoSolicitudTransformer extends TransformerAbstract
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

    public function transform( $model)
    {
        return [
            'id' => $model->getKey()
        ];
    }
}