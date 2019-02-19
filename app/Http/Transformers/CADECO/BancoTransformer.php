<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/02/2019
 * Time: 07:03 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Banco;
use League\Fractal\TransformerAbstract;

class BancoTransformer extends TransformerAbstract
{
    public function transform(Banco $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }
}