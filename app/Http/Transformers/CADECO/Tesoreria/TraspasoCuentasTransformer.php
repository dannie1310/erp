<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:29 PM
 */

namespace App\Http\Transformers\CADECO\Tesoreria;


use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use League\Fractal\TransformerAbstract;

class TraspasoCuentasTransformer extends TransformerAbstract
{
    public function transform(TraspasoCuentas $model) {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio
        ];
    }
}