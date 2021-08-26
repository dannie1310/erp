<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativo;
use League\Fractal\TransformerAbstract;

class CuentaSaldoNegativoTransformer extends TransformerAbstract
{
    public function transform(CuentaSaldoNegativo $model) {
        return [
            'id' => (int) $model->id,
            'base_datos' => $model->base_datos,
            'nombre_empresa' => $model->nombre_empresa,
            'codigo_cuenta' => $model->codigo_cuenta,
            'nombre_cuenta' => $model->nombre_cuenta,
            'saldo_cuenta' => $model->saldo_format,
            'fecha_actualizacion' => $model->fecha_actualizacion,
            'tipo' => $model->tipo,
            'tipo_cuenta' => $model->tipoCuenta->descripcion,
        ];
    }

}
