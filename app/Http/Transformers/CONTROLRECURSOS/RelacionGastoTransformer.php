<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\RelacionGasto;
use League\Fractal\TransformerAbstract;

class RelacionGastoTransformer extends TransformerAbstract
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

    public function transform(RelacionGasto $model){
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio,
            'fecha' => $model->fecha_inicio,
            'fecha_inicio_format' => $model->fecha_inicio_format,
            'total_format' => $model->total_format,
            'total' => $model->total,
            'moneda' => $model->moneda_descripcion,
            'serie' => $model->serie_descripcion,
            'id_serie' => $model->idserie,
            'id_empleado' => $model->idempleado,
            'id_empresa' => $model->idempresa,
            'id_proyecto' => $model->idproyecto,
            'empresa_descripcion' => $model->empresa_descripcion,
            'empleado_descripcion' => $model->empleado_descripcion,
            'proyecto_descripcion' => $model->proyecto_descripcion,
            'estado' => $model->idestado,
            'estado_descripcion' => $model->estatus_descripcion,
            'estado_color' => $model->color_estado,
        ];
    }
}
