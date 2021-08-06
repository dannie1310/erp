<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivo;
use League\Fractal\TransformerAbstract;

class CtgTipoArchivoTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
    ];

    public function transform(CtgTipoArchivo $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
        ];
    }
}
