<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Documentacion;


use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use League\Fractal\TransformerAbstract;

class CtgTipoTransaccionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
    ];

    public function transform(CtgTipoTransaccion $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'estatus' => $model->estatus,
        ];
    }
}
