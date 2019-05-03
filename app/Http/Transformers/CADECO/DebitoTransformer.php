<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 06:16 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Debito;
use League\Fractal\TransformerAbstract;

class DebitoTransformer extends TransformerAbstract
{
    public function transform(Debito $model)
    {
        return $model->toArray();
    }
}