<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Material;
use League\Fractal\TransformerAbstract;

class MaterialTransformer extends TransformerAbstract
{
    public function transform(Material $model) {
        return  [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion
        ];
    }
}