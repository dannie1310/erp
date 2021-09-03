<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 12/03/2020
 * Time: 08:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use App\Http\Transformers\EstadoLabelTransformer;
use App\Models\CADECO\ControlPresupuesto\Extraordinario;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;

class ExtraordinarioTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario',
        'partidas'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'usuario',
        'estado_label'
    ];

    public function transform(Extraordinario $model) {
        return [
            'id' => (int) $model->getKey(),
            'area_solicitante' => (string) $model->area_solicitante,
            'motivo' => (string) $model->motivo,
            'numero_folio' =>  $model->numero_folio,
            'numero_folio_format' =>  $model->numero_folio_format,
            'id_estatus' => (int) $model->id_estatus,
            'estatus' => $model->estatus->descripcion,
            'id_tipo_orden' => (int) $model->id_tipo_orden,
            'tipo_orden' => $model->tipoOrden->descripcion,
            'importe_afectacion' => $model->importe_afectacion,
            'importe_original' => $model->importe_original,
            'importe_afectacion_format' => $model->importe_afectacion_format,
            'importe_original_format' => $model->importe_original_format,
            'fecha_solicitud' => $model->fecha_solicitud,
            'fecha_solicitud_format' => $model->fecha_solicitud_format,
            'fecha_format' => $model->fecha_solicitud_format,
            'porcentaje_cambio_format' => $model->porcentaje_cambio_format,
            'porcentaje_cambio' => $model->porcentaje_cambio,
        ];
    }

    /**
     * @param VariacionVolumen $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(Extraordinario $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

     /**
     * @param VariacionVolumen $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePartidas(Extraordinario $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new ExtraordinarioTransformer);
        }
        return null;
    }

    public function includeEstadoLabel(Extraordinario $model)
    {
        if ($item = $model->estado_label) {
            return $this->item($item, new EstadoLabelTransformer);
        }
        return null;
    }
}
