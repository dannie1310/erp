<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Documentacion;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;

class ArchivoTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'tipo_archivo'

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
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
            'nombre' => $model->nombre,
            'estatus' => $model->estatus,
            'extension' => $model->extension,
            'observaciones' => $model->observaciones
        ];
    }

    public function includeTipoArchivo(Archivo $model)
    {
        if($item = $model->ctgTipoArchivo)
        {
            return $this->item($item, new CtgTipoArchivoTransformer);
        }
        return null;
    }

    public function getRegistroAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    public function getFechaRegistroFormatAttribute()
    {
        if($this->fecha_hora_registro){
            $date = date_create($this->fecha_hora_registro);
            return date_format($date,"d/m/Y H:i");
        }
        return '';
    }
}
