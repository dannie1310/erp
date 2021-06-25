<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use League\Fractal\TransformerAbstract;
use App\Models\ACARREOS\OperadorHistorico;

class OperadorHistoricoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(OperadorHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'clave_format' => $model->clave_format,
            'nombre' => (string) $model->Nombre,
            'direccion' => (string) $model->Direccion,
            'no_licencia' => (string) $model->NoLicencia,
            'vigencia_licencia_format' => (string) $model->vigencia_licencia_format,
            'fecha_alta_format' => (string) $model->fecha_alta_format,
            'fecha_baja_format' => (string) $model->fecha_baja_format,
            'registro' => (string) $model->nombre_registro,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_completa_format,
            'desactivo' => (string) $model->nombre_desactivo,
            'fecha_desactivo_format' => $model->fecha_desactivo_format,
            'motivo' => $model->motivo
        ];
    }
}
