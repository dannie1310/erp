<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 22/02/2019
 * Time: 11:29 AM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\CtgTipoSolicitud;
use League\Fractal\TransformerAbstract;

class CtgTipoSolicitudTransformer extends TransformerAbstract
{
    /**
     * @param CtgTipoSolicitud $model
     * @return array
     */
    public function transform(CtgTipoSolicitud $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => (string)$model->descripcion,
        ];
    }
}