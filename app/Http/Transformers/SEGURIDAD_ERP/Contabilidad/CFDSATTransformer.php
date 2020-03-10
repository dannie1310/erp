<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 12:08 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use League\Fractal\TransformerAbstract;

class CFDSATTransformer extends TransformerAbstract
{
    public function transform(CFDSAT $model) {
        return [
            'id' => (int) $model->id,
            'serie'=>$model->serie,
            'folio'=>$model->folio,
            'fecha'=>$model->fecha,
            'rfc_emisor' => $model->rfc_emisor,
            'rfc_receptor' => $model->rfc_receptor,
            'iva' => $model->importe_iva,
            'porcentaje_iva' => $model->porcentaje_iva,
            'uuid' => $model->uuid,
        ];
    }

}