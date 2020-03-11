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
            'concepto_original' => $model->concepto_original,
        ];
    }

}