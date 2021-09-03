<?php
/**
 * Created by PhpStorm.
 * User: JlopezA
 * Date: 12/03/2020
 * Time: 08:44 PM
 */

namespace App\Http\Transformers\CADECO\ControlPresupuesto;

use League\Fractal\TransformerAbstract;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumenPartidas;

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

    public function transform(VariacionVolumenPartidas $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_tipo_orden' => (int) $model->id_tipo_orden,
            'tipo_orden' => (string) $model->solicitudcambio->estatus->descripcion,
            'unidad' =>  $model->unidad,
            'clave_concepto' => (string) $model->concepto->clave_concepto,
            'descripcion' => (string) $model->concepto->descripcion,
            'descripcion_format' => (string) $model->descripcion_format,
            'cantidad_presupuestada_original' => $model->cantidad_presupuestada_original,
            'cantidad_presupuestada_original_format' => $model->cantidad_presupuestada_original_format,
            'cantidad_presupuestada_nueva' =>  $model->cantidad_presupuestada_nueva,
            'cantidad_presupuestada_nueva_format' => $model->cantidad_presupuestada_nueva_format,
            'precio_unitario_original' => $model->precio_unitario_original,
            'precio_unitario_original_format' => $model->precio_unitario_original_format,
            'importe_original_format' => $model->importe_original_format,
            'importe_cambio_format' => $model->importe_cambio_format,
            'importe_actualizado_format' => $model->importe_actualizado_format,
            'variacion_volumen' => $model->variacion_volumen,
            'variacion_volumen_format' => $model->variacion_volumen_format,
        ];
    }
}
