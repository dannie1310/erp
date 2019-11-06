<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:08 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Compras\SolicitudComplemento;
use League\Fractal\TransformerAbstract;

class SolicitudComplementoTransformer extends TransformerAbstract
{
    public function transform(SolicitudComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'id_area_compradora' => $model->id_area_compradora,
            'id_tipo' => $model->id_tipo,
            'id_area_solicitante' => $model->id_area_solicitante,
            'folio' => $model->folio_compuesto,
            'estado' => $model->estado,
            'concepto' => $model->concepto,
            'fecha_requisicion_origen' => $model->fecha_requisicion_origen,
            'requisicion_origen' => $model->requisicion_origen,
            'registro' => $model->registro,
            'fecha_registro' =>$model->timestamp_registro,

        ];
    }
}
