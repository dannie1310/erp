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
            'obligatorio' => $model->obligatorio,
            'tipo_documento' => $model->ctgTipoArchivo->tipo_documento,
            'especificacion' => $model->ctgTipoArchivo->especificaciones,
            'tipo_archivo_descripcion' => $model->descripcion_complementada,
            'tipo_archivo_descripcion_larga' => $model->ctgTipoArchivo->descripcion_larga,
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
            'nombre_archivo_format' => $model->nombre_archivo_format,
            'nombre_archivo' => $model->nombre_archivo.".".$model->extension_archivo,
            'estatus' => $model->estatus,
            'seccion' => $model->ctgTipoArchivo->ctgSeccion->descripcion,
            'id_area' => (int)$model->ctgTipoArchivo->id_area,
        ];
    }
}
