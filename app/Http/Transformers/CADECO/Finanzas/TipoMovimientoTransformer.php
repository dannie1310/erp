<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 07:54 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Tesoreria\TipoMovimiento;
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