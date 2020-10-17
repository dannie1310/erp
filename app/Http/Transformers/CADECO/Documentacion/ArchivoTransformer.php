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
            'tipo_archivo' => $model->tipoArchivo->descripcion,
            'categoria' => $model->categoria->descripcion,
            'nombre_archivo' => $model->nombre,
            'extension' => $model->extension,
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
        ];
    }
}
