<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 12/03/2020
 * Time: 08:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use App\Models\CADECO\ControlPresupuesto\ExtraordinarioPartidas;
use League\Fractal\TransformerAbstract;

class ExtraordinarioPartidasTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(ExtraordinarioPartidas $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_tipo_orden' => (int) $model->id_tipo_orden,
            'tipo_orden' => (string) $model->solicitudcambio->estatus->descripcion,
            'unidad' =>  $model->unidad,
            'descripcion' => (string) $model->descripcion,
            'descripcion_format' => (string) $model->descripcion_format,
            'cantidad' =>  $model->cantidad_presupuestada_nueva,
            'cantidad_format' => $model->cantidad_format,
            'precio_unitario' => $model->precio_unitario_nuevo,
            'precio_unitario_format' => $model->precio_unitario_format,
            'importe_format' => $model->importe_format,
            'longitud_nivel'=>$model->longitud_nivel,
            'clave'=>$model->clave_concepto
        ];
    }
}
