<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:58 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use League\Fractal\TransformerAbstract;

class SolicitudEdicionTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'partidas'
    ];

    public function transform(SolicitudEdicion $model)
    {
        return [
            'id' => (int)$model->id,
            'numero_folio_format' => $model->numero_folio_format,
            'fecha_hora_registro_format' => $model->fecha_hora_registro_format,
            'usuario_registro' => $model->usuario_registro->nombre_completo,
            'estado_format' => $model->estado_format,
            'estado' => $model->estado,
            'numero_bd' => $model->numero_bd,
            'numero_polizas' => $model->polizas()->count(),
            'numero_partidas' => $model->partidas()->count(),
            'numero_movimientos' => $model->numero_movimientos,
            'fecha_hora_autorizacion_format' => $model->fecha_hora_autorizacion_format,
            'usuario_autorizo' => ($model->usuario_autorizo) ? $model->usuario_autorizo->nombre_completo : "",
            'fecha_hora_rechazo_format' => $model->fecha_hora_rechazo_format,
            'usuario_rechazo' => ($model->usuario_rechazo) ? $model->usuario_rechazo->nombre_completo : "",
            'fecha_hora_aplicacion_format' => $model->fecha_hora_aplicacion_format,
            'usuario_aplico' => ($model->usuario_aplico) ? $model->usuario_aplico->nombre_completo : "",
        ];
    }

    public function includePartidas(SolicitudEdicion $model)
    {
        if ($partidas = $model->partidas) {
            return $this->collection($partidas, new SolicitudEdicionPartidaTransformer);
        }
    }
}