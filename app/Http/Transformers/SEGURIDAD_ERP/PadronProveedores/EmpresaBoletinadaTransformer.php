<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinada;
use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\ArchivoTransformer;

class EmpresaBoletinadaTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(EmpresaBoletinada $model)
    {
        return [
            'id' => $model->rfc,
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'tipo' => $model->tipo,
            'motivo' => $model->motivo,

        ];
    }
}
