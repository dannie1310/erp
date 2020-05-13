<?php

/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:16 PM
 */


namespace App\Http\Transformers\SEGURIDAD_ERP\IncidentesPolizas;


use App\Http\Transformers\CTPQ\PolizaTransformer;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\IncidentesPolizas\IncidenteIndividualConsolidada as Model;
use League\Fractal\TransformerAbstract;

class IncidenteIndividualConsolidadaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'poliza'
    ];

    public function transform(Model $model)
    {
        return [
            'id'=> $model->getKey(),
            'tipo_incidente' =>$model->tipo_incidente->descripcion,
            'fecha_hora_deteccion_format'=>$model->fecha_hora_deteccion_format,
            'fecha_hora_resolucion_format'=>$model->fecha_hora_resolucion_format,
            'base_datos'=>$model->base_datos
        ];
    }

    public function includePoliza(Model $model)
    {
        if($poliza = $model->poliza)
        {
            return $this->item($poliza, new PolizaTransformer);
        }
        return null;
    }
}
