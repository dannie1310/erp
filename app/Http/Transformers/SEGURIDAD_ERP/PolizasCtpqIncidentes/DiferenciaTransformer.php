<?php

/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:16 PM
 */


namespace App\Http\Transformers\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Http\Transformers\CTPQ\PolizaTransformer;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia as Model;
use League\Fractal\TransformerAbstract;

class DiferenciaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'poliza'
    ];

    public function transform(Model $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_incidente' => $model->tipo->descripcion,
            'fecha_hora_deteccion_format' => $model->fecha_hora_deteccion_format,
            'fecha_hora_resolucion_format' => $model->fecha_hora_resolucion_format,
            'base_datos' => $model->base_datos,
            'base_datos_referencia' => $model->base_datos_referencia
        ];
    }

    public function includePoliza(Model $model)
    {
        if ($poliza = $model->poliza) {
            return $this->item($poliza, new PolizaTransformer);
        }
        return null;
    }
}
