<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 09:02 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartida;
use League\Fractal\TransformerAbstract;

class SolicitudEdicionPartidaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'polizas'
    ];
    public function transform(SolicitudEdicionPartida $model) {
        return [
            'id' => (int) $model->id,
            'fecha' => $model->fecha_format,
            'folio' => $model->folio,
            'tipo' => $model->tipo,
            'importe' => $model->importe,
            'concepto' => $model->concepto,
            'referencia' => $model->referencia,
        ];
    }

    public function includePolizas(SolicitudEdicionPartida $model)
    {
        if($polizas = $model->polizas)
        {
            return $this->collection($polizas, new SolicitudEdicionPartidaPolizaTransformer);
        }
    }

}