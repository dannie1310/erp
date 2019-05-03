<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 06:16 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Credito;
use League\Fractal\TransformerAbstract;

class CreditoTransformer extends TransformerAbstract
{
    public function transform(Credito $model)
    {
        return $model->toArray();
    }
}