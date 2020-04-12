<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/11/2019
 * Time: 02:00 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Compras\SolicitudPartidaComplemento;
use League\Fractal\TransformerAbstract;

class SolicitudPartidaComplementoTransformer extends TransformerAbstract
{
    public function transform(SolicitudPartidaComplemento $model)
    {
        return [
            'id_item' => $model->getKey(),
            'observaciones' => $model->observaciones
        ];
    }
}
