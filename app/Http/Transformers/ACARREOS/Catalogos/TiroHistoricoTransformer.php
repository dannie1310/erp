<?php


namespace App\Http\Transformers\ACARREOS\Catalogos;


use App\Models\ACARREOS\TiroHistorico;
use League\Fractal\TransformerAbstract;

class TiroHistoricoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];


    public function transform(TiroHistorico $model) {
        return [
            'id' => (int) $model->getKey(),
            'clave' => $model->Clave,
            'clave_format' => $model->clave_format,
            'descripcion' => (string) $model->Descripcion,
            'usuario_registro' => (string) $model->nombre_registro,
            'usuario_desactivo' => (string) $model->nombre_desactivo,
            'estado' => (int) $model->Estatus,
            'estado_format' => $model->estado_format,
            'fecha_registro_format' => $model->fecha_registro_format,
            'fecha_desactivo_format' => $model->fecha_desactivo_format,
            'motivo' => $model->motivo
        ];
    }
}
