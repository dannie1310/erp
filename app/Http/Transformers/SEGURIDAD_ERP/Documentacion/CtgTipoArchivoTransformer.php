<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Documentacion;


use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoArchivo;
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
