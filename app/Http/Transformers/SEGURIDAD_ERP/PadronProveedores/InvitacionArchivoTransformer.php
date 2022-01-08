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
            'tipo_archivo_txt' => $model->tipo_archivo_txt,
            'categoria' => '',
            'nombre' => $model->nombre,
            'extension' => $model->extension,
            'observaciones' => $model->observaciones,
            'observaciones_format' => $model->observaciones_format,
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
            'registro' => $model->registro,
            'fecha_registro_format' => $model->fecha_registro_format,
            'tipo_transaccion' => "Invitación a ".$model->invitacion->tipo_invitacion,
            'folio_transaccion' => $model->invitacion->numero_folio_format,
            'observaciones_transaccion' => $model->invitacion->observaciones,
            'icono_transaccion' => $model->invitacion->icono,
            'id_transaccion' => $model->invitacion->id,
            'eliminable' => 1,
        ];
    }

    public function includeTipoArchivo(InvitacionArchivo $model)
    {
        if($item = $model->tipo)
        {
            return $this->item($item, new CtgTipoArchivoInvitacionTransformer);
        }
        return null;
    }
}
