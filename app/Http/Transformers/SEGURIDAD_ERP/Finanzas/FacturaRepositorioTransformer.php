<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;

use App\Http\Transformers\CADECO\Finanzas\FacturaTransformer;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use League\Fractal\TransformerAbstract;

class FacturaRepositorioTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'factura'
    ];

    public function transform(FacturaRepositorio $model) {
        return [
            'id' => (int) $model->id,
            'base_datos' =>$model->proyecto->base_datos,
            'obra'=>$model->obra,
            'fecha_hora_carga_format'=>$model->fecha_hora_registro_format
        ];
    }

    public function includeFactura(FacturaRepositorio $model)
    {
        if($item = $model->transaccion_factura)
        {
            return $this->item($item, new FacturaTransformer);
        }
        return null;
    }

}
