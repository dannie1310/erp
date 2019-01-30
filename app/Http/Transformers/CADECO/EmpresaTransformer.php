<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:06 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    public function transform(Empresa $model) {
        return  [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }
}