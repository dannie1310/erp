<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use League\Fractal\TransformerAbstract;
use App\Models\ACARREOS\TelefonoHistorico;

class TelefonoHistoricoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(TelefonoHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'imei' => $model->imei,
            'linea' => $model->linea,
            'id_dispositivo' => $model->device_id,
            'marca' => (string) $model->marca,
            'modelo' => (string) $model->modelo,
            'checador' => (string) $model->nombre_checador,
            'usuario_registro' => (string) $model->nombre_registro,
            'usuario_desactivo' => (string) $model->nombre_desactivo,
            'estado' => (int) $model->estatus,
            'color_estado' => (int) $model->estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_format,
            'fecha_desactivo_format' => $model->fecha_desactivo_format,
            'motivo' => $model->motivo
        ];
    }
}
