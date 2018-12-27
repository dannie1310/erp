<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/12/18
 * Time: 11:13 AM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TIpoPolizaContpaq;
use League\Fractal\TransformerAbstract;

class TipoPolizaContpaqTransformer extends TransformerAbstract
{
    public function transform(TIpoPolizaContpaq $model) {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}