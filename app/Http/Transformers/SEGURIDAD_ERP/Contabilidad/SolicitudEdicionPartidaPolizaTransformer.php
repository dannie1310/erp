<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 09:12 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionPartidaPoliza;
use League\Fractal\TransformerAbstract;

class SolicitudEdicionPartidaPolizaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'movimientos'
    ];

    public function transform(SolicitudEdicionPartidaPoliza $model) {
        return [
            'id' => (int) $model->id,
            'bd_contpaq' => $model->bd_contpaq,
            'empresa_contpaq' => $model->empresa->Nombre    ,
            'concepto_original' => $model->concepto_original,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format,
            'numero_movimientos' => $model->movimientos()->count(),
            'estado' => ($model->estado == 0)?false:$model->estado,
            'class_estado' => ($model->estado == 0)?"far fa-square":"fa fa-check-square",
        ];
    }

    public function includeMovimientos(SolicitudEdicionPartidaPoliza $model)
    {
        if($movimientos = $model->movimientos)
        {
            return $this->collection($movimientos, new SolicitudEdicionPartidaPolizaMovimientoTransformer);
        }
    }

}