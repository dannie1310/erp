<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 09:08 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\FinanzasCBE\CtgTipoSolicitud;
use League\Fractal\TransformerAbstract;

class CtgTipoSolicitudTransformer extends TransformerAbstract
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

    public function transform(CtgTipoSolicitud $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}