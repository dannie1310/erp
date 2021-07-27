<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivoTransformer;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo;
use League\Fractal\TransformerAbstract;


class InvitacionArchivoTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'tipo_archivo'
    ];

    protected $availableIncludes = [
        'tipo_archivo'
    ];

    public function transform(InvitacionArchivo $model)
    {
        return [
            'id' => $model->getKey(),
            'tipo_archivo' => $model->id_tipo_archivo,
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
            'nombre' => $model->nombre,
            'estatus' => $model->estatus,
            'extension' => $model->extension,
            'observaciones' => $model->observaciones
        ];
    }

    public function includeTipoArchivo(InvitacionArchivo $model)
    {
        if($item = $model->tipo)
        {
            return $this->item($item, new CtgTipoArchivoTransformer);
        }
        return null;
    }
}
