<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 28/10/2019
 * Time: 07:21 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Entrega;
use League\Fractal\TransformerAbstract;

class EntregaTransformer extends TransformerAbstract
{
    public function transform(Entrega $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_entrega' => $model->numero_entrega,
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format
        ];
    }
}