<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 04/03/2019
 * Time: 06:15 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\CtgTipoMovimientoFondoGarantia;
use League\Fractal\TransformerAbstract;

class CtgTipoMovimientoFondoGarantiaTransformer extends TransformerAbstract
{
    /**
     * @param CtgTipoMovimientoFondoGarantia $model
     * @return array
     */
    public function transform(CtgTipoMovimientoFondoGarantia $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'descripcion' => (string)$model->descripcion,
            'naturaleza' => (int)$model->naturaleza,
            'naturaleza_desc' => (string)($model->naturaleza == 1)?'Acreedora':'Deudora',
        ];
    }

}