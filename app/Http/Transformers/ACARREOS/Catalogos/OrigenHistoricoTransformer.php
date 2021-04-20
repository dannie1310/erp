<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\OrigenHistorico;
use League\Fractal\TransformerAbstract;

class OrigenHistoricoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(OrigenHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'clave_format' => $model->clave_format,
            'descripcion' => (string) $model->Descripcion,
            'registro' => (string) $model->nombre_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'tipo_origen' => $model->tipo_origen_format,
            'id_tipo' => $model->interno,
            'tipo' => $model->tipo_origen_descripcion,
            'desactivo' => (string) $model->nombre_desactivo,
            'fecha_desactivo_format' => $model->fecha_desactivo_format,
            'motivo' => $model->Motivo
        ];
    }
}
