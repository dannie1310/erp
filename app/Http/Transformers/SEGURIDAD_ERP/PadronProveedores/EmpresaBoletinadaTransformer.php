<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Views\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaVw;
use League\Fractal\TransformerAbstract;

class EmpresaBoletinadaTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(EmpresaBoletinadaVw $model)
    {
        return [
            'id' => $model->id,
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'motivo' => $model->motivo,
            'observaciones' => $model->observaciones,
            'editable' => $model->editable,
            'id_motivo' => $model->id_motivo,
        ];
    }
}
