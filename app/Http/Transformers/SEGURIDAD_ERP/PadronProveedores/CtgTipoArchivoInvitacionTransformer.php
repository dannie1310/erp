<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivoInvitacion;
use League\Fractal\TransformerAbstract;

class CtgTipoArchivoInvitacionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
    ];

    public function transform(CtgTipoArchivoInvitacion $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
        ];
    }
}
