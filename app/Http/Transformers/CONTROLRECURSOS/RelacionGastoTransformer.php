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
        'documentos'
    ];

    public function transform(RelacionGasto $model){
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio,
            'fecha' => $model->fecha_inicio,
            'fecha_inicio_format' => $model->fecha_inicio_format,
            'fecha_final_format' => $model->fecha_final_format,
            'fecha_inicio_editar' => $model->fecha_inicio_editar,
            'fecha_final_editar' => $model->fecha_final_editar,
            'moneda' => $model->moneda_descripcion,
            'serie' => $model->serie_descripcion,
            'id_serie' => $model->idserie,
            'id_empleado' => $model->idempleado,
            'id_empresa' => $model->idempresa,
            'id_proyecto' => $model->idproyecto,
            'id_moneda' =>$model->idmoneda,
            'empresa_descripcion' => $model->empresa_descripcion,
            'empleado_descripcion' => $model->empleado_descripcion,
            'proyecto_descripcion' => $model->proyecto_descripcion,
            'estado' => $model->idestado,
            'estado_descripcion' => $model->estatus_descripcion,
            'estado_color' => $model->color_estado,
            'departamento' => $model->departamento_descripcion,
            'motivo' => $model->motivo,
            'suma_importe' => $model->suma_importe,
            'suma_importe_format' => $model->suma_importe_format,
            'suma_iva' => $model->suma_iva,
            'suma_iva_format' => $model->suma_iva_format,
            'suma_retenciones' => $model->suma_retenciones,
            'suma_retenciones_format' => $model->suma_retenciones_format,
            'suma_otros_imp' => $model->suma_otros_imp,
            'suma_otros_imp_format' => $model->suma_otros_imp_format,
            'total' => $model->total,
            'total_format' => $model->total_format,
            'fecha_inicio' => $model->fecha_inicio,
            'fecha_final' => $model->fecha_fin,
        ];
    }

    /**
     * @param RelacionGasto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumentos(RelacionGasto $model)
    {
        if($documentos = $model->documentos)
        {
            return $this->collection($documentos, new RelacionGastoDocumentoTransformer);
        }
        return null;
    }
}
