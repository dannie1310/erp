<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:37 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    public function transform(Empresa $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->razon_social,
            'rfc' => (string) $model->rfc,
        ];
    }
}