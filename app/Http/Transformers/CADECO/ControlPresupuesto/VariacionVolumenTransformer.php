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
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Http\Transformers\CADECO\ControlPresupuesto\VariacionVolumenPartidasTransformer;

class VariacionVolumenTransformer extends TransformerAbstract
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
        'usuario'
    ];

    public function transform(VariacionVolumen $model) {
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
            'importe_afectacion_format' => $model->importe_afectacion_format,
            'fecha_solicitud' => $model->fecha_solicitud,
            'fecha_solicitud_format' => $model->fecha_solicitud_format,
        ];
    }

    /**
     * @param VariacionVolumen $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(VariacionVolumen $model)
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
    public function includePartidas(VariacionVolumen $model)
    {
        if($partidas = $model->variacionVolumenPartidas)
        {
            return $this->collection($partidas, new VariacionVolumenPartidasTransformer);
        }
        return null;
    }
}
