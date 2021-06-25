<?php


namespace App\Http\Transformers\CADECO\Documentacion;


use League\Fractal\TransformerAbstract;
use \App\Models\CADECO\Documentacion\Archivo;

class ArchivoTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(Archivo $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_archivo' => $model->tipo_archivo_txt,
            'categoria' => $model->categoria->descripcion,
            'nombre' => $model->nombre,
            'extension' => $model->extension,
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
            'tipo_transaccion' => $model->transaccion->tipo_transaccion_str,
            'folio_transaccion' => $model->transaccion->numero_folio_format,
            'observaciones_transaccion' => $model->transaccion->observaciones,
            'icono_transaccion' => $model->transaccion->icono,
            'id_transaccion' => $model->transaccion->id_transaccion,
            'eliminable' => $model->eliminable,
        ];
    }
}
