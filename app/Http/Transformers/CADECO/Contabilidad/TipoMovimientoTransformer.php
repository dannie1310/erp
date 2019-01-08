<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 07:31 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoMovimiento;
use League\Fractal\TransformerAbstract;

class TipoMovimientoTransformer extends TransformerAbstract
{
    public function transform(TipoMovimiento $model) {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}