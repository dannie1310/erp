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

    public function transform(SolicitudEdicion $model) {
        return [
            'id' => (int) $model->id,
            'numero_folio_format' => $model->numero_folio_format,
            'fecha_hora_registro_format' => $model->fecha_hora_registro_format,
            'usuario_registro' => $model->usuario->nombre_completo,
            'estado_format' => $model->estado_format,
            'numero_bd' => $model->numero_bd,
            'numero_polizas' => $model->polizas()->count(),
            'numero_partidas' => $model->partidas()->count(),
            'numero_movimientos' => $model->numero_movimientos,
        ];
    }

    public function includePartidas(SolicitudEdicion $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new SolicitudEdicionPartidaTransformer);
        }
    }

}