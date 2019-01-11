<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 8/01/19
 * Time: 07:53 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaContable;
use League\Fractal\TransformerAbstract;

class CuentaContableTransformer extends TransformerAbstract
{
    public function transform(CuentaContable $model) {
        return [
            'id' => $model->getKey(),
            'cuenta_contable' => $model->cuenta_contable
        ];
    }

}