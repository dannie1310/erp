<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgSeccion;

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
            'tipo_archivo' => $model->id_tipo_archivo,
            'obligatorio' => (bool)$model->ctgTipoArchivo->obligatorio,
            'tipo_documento' => $model->ctgTipoArchivo->tipo_documento,
            'tipo_archivo_descripcion' => $model->ctgTipoArchivo->descripcion,
            'tipo_archivo_descripcion_corta' => substr($model->ctgTipoArchivo->descripcion, 0, 55) ,
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
            'nombre_archivo_format' => $model->nombre_archivo_format,
            'nombre_archivo' => $model->nombre_archivo,
            'estatus' => $model->estatus,
            'seccion' => $model->ctgTipoArchivo->ctgSeccion->descripcion,
        ];
    }
}