<?php


namespace App\Http\Transformers\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinFacIngresoFactura;
use League\Fractal\TransformerAbstract;

class FacturaTransformer extends TransformerAbstract
{
    public function transform(FinFacIngresoFactura $model) {
        return [
            'id' => (int) $model->getKey(),
            'nombre_proyecto' => $model->nombre_proyecto,
            'nombre_cliente' => $model->nombre_cliente,
            'nombre_empresa' => $model->nombre_empresa,
            'nombre_moneda' => $model->nombre_moneda,
            'numero' => $model->numero,
            'fecha_format' => $model->fecha_format,
            'fecha_cobro_format' => $model->fecha_cobro_format,
            'fecha_fi_format' => $model->fecha_fi_format,
            'fecha_ff_format' => $model->fecha_ff_format,
            'importe_format' => $model->importe_format,
            'descripcion' => $model->descripcion,
            'tipo_cambio' => $model->tipo_cambio,
            'estado' => $model->estado,
            'estado_descripcion' => $model->estado_descripcion,
            'estado_color' => $model->estado_color
        ];
    }
}
