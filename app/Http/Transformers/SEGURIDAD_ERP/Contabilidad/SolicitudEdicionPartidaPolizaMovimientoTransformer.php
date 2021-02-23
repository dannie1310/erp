<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 09:33 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartidaPolizaMovimiento;
use League\Fractal\TransformerAbstract;

class SolicitudEdicionPartidaPolizaMovimientoTransformer extends TransformerAbstract
{
    public function transform(SolicitudEdicionPartidaPolizaMovimiento $model) {
        return [
            'id' => (int) $model->id,
            'referencia_original' => $model->referencia_original,
            'concepto_original' => $model->concepto_original,
        ];
    }

}