<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;

use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use League\Fractal\TransformerAbstract;

class FacturaRepositorioTransformer extends TransformerAbstract
{
    public function transform(FacturaRepositorio $model) {
        return [
            'id' => (int) $model->id,
            'base_datos' =>$model->proyecto->base_datos,
            'obra'=>$model->obra,
            'fecha_hora_carga_format'=>$model->fecha_hora_registro_format
        ];
    }
}
