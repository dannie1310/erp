<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 12/03/2020
 * Time: 08:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use League\Fractal\TransformerAbstract;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;

class SolicitudCambioTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'usuario'
    ];

    public function transform(SolicitudCambio $model) {
        return [
            'id' => (int) $model->getKey(),
            'area_solicitante' => (string) $model->area_solicitante,
            'numero_folio' =>  $model->numero_folio,
            'id_estatus' => (int) $model->id_estatus,
            'estatus' => $model->estatus->descripcion,
            'id_tipo_orden' => (int) $model->id_tipo_orden,
            'tipo_orden' => $model->tipoOrden->descripcion,
            'importe_afectacion' => $model->importe_afectacion,
            'importe_afectacion_format' => $model->importe_afectacion_format,
            'fecha_solicitud' => $model->fecha_solicitud,
        ];
    }

    /**
     * @param SolicitudCambio $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(SolicitudCambio $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}