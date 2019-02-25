<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 25/02/2019
 * Time: 01:35 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CierreApertura;
use League\Fractal\TransformerAbstract;

class CierreAperturaTransformer extends TransformerAbstract
{
    /**
     * @param CierreApertura $model
     * @return array
     */
    public function transform(CierreApertura $model) {
        return [
            'id' => (int) $model->id_cierre,
            'estatus' => $model->estado
        ];
    }
}