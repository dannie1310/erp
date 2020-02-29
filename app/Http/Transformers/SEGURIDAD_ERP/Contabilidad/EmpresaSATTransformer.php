<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:43 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;

class EmpresaSATTransformer extends TransformerAbstract
{
    public function transform(EmpresaSAT $model) {
        return [
            'id' => (int) $model->id,
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
        ];
    }

}