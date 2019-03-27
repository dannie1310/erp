<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 22/02/2019
 * Time: 03:47 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\CtgTipoMovimientoSolicitud;
use League\Fractal\TransformerAbstract;

class CtgTipoMovimientoSolicitudTransformer extends TransformerAbstract
{
    /**
     * @param CtgTipoMovimientoSolicitud $model
     * @return array
     */
    public function transform(CtgTipoMovimientoSolicitud $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => (string)$model->descripcion,
            'estado_resultante_desc' => (string)$model->estado_resultante_desc,
        ];
    }
}