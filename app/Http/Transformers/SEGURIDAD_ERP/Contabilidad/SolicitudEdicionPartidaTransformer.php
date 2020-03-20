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
        'polizas',
        'polizas_autorizadas'
    ];
    public function transform(SolicitudEdicionPartida $model) {
        return [
            'id' => (int) $model->id,
            'fecha_format' => $model->fecha_format,
            'folio' => $model->folio,
            'tipo' => $model->tipo,
            'tipo_format' => $model->tipo_format,
            'importe' => $model->importe,
            'importe_format' => $model->importe_format,
            'concepto' => $model->concepto,
            'referencia' => $model->referencia,
            'numero_bd' => $model->numero_bd,
            'numero_polizas' => $model->polizas()->count(),
            'numero_movimientos' => $model->movimientos()->count(),
        ];
    }

    public function includePolizas(SolicitudEdicionPartida $model)
    {
        if($polizas = $model->polizas)
        {
            return $this->collection($polizas, new SolicitudEdicionPartidaPolizaTransformer);
        }
    }

    public function includePolizasAutorizadas(SolicitudEdicionPartida $model)
    {
        if($polizas = $model->polizasAutorizadas)
        {
            return $this->collection($polizas, new SolicitudEdicionPartidaPolizaTransformer);
        }
    }

}