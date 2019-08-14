<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 09:08 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\FinanzasCBE\CtgTipoMovimientoSolicitud;
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

    public function transform(CtgTipoMovimientoSolicitud $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => (string)$model->descripcion,
            'estado_resultante_desc' => (string)$model->estado_resultante_desc,
        ];
    }
}