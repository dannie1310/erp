<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 01:42 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\MaterialFamilia;
use League\Fractal\TransformerAbstract;

class MaterialTransformer extends TransformerAbstract
{
    public function transform(MaterialFamilia $model)
    {
//        dd($model->descripcion);
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'unidad' => $model->unidad,
            'tipo_material' => $model->tipo_material,
            'tipo_material_descripcion' => $model->tipo_material_descripcion
        ];
    }


}
