<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 05:42 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TransaccionInterfaz;
use League\Fractal\TransformerAbstract;

class TransaccionInterfazTransformer extends TransformerAbstract
{
    public function transform(TransaccionInterfaz $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->descripcion
        ];
    }
}