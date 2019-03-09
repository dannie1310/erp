<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Estimacion;
use League\Fractal\TransformerAbstract;

class EstimacionTransformer extends TransformerAbstract
{


    public function transform(Estimacion $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'observaciones' => $model->observaciones
        ];
    }

}