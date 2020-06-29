<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 09:02 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Http\Transformers\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartida;
use League\Fractal\TransformerAbstract;

class SolicitudEdicionPartidaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'polizas',
        'polizas_autorizadas',
        'diferencia'
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
            'numero_polizas' => $model->numero_polizas_format,
            'numero_cuentas' => $model->numero_cuentas_format,
            'numero_movimientos' => $model->numero_movimientos_format,
            'estado' => ($model->estado == 0)?false:$model->estado,
            'class_estado' => ($model->estado == 0)?"far fa-square":"fa fa-check-square",
        ];
    }

    public function includePolizas(SolicitudEdicionPartida $model)
    {
        if($polizas = $model->polizas)
        {
            return $this->collection($polizas, new SolicitudEdicionPartidaPolizaTransformer);
        }
    }

    public function includeDiferencia(SolicitudEdicionPartida $model)
    {
        if($diferencia = $model->diferencia)
        {
            return $this->item($diferencia, new DiferenciaTransformer);
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