<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;

use App\Models\CADECO\Finanzas\SolicitudPagoAutorizacion;
use League\Fractal\TransformerAbstract;

class SolicitudPagoAutoizacionTransformer extends TransformerAbstract
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

    public function transform(SolicitudPagoAutorizacion $model)
    {
        return [
            'id' => $model->getKey(),
            'usuario_registro' => $model->usuario_registro,
            'usuario_autorizo' => $model->usuario_autorizo,
            'usuario_rechazo' => $model->usuario_rechazo

        ];
    }

}
