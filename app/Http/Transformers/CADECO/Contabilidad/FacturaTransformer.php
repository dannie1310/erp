<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:16 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\TransaccionTransformer;
use App\Models\CADECO\Transaccion;

class FacturaTransformer extends TransaccionTransformer
{
    public function transform(Transaccion $model) {
           return $model->toArray();
    }
}