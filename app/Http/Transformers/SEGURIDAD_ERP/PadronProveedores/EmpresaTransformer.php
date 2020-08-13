<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [

    ];

    public function transform(Empresa $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'estado_expediente' => $model->estado_expediente->descripcion,
            'avance_expediente' => $model->avance_expediente,
            'archivos_esperados' => $model->no_archivos_esperados,
            'archivos_cargados' => $model->no_archivos_cargados,
            'porcentaje_avance_expediente' => $model->porcentaje_avance_expediente,
            'usuario_inicio' => $model->usuario_inicio->nombre_completo,
        ];
    }
}
