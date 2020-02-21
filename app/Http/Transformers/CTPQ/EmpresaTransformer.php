<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:57 AM
 */

namespace App\Http\Transformers\CTPQ;


use App\Models\CTPQ\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    public function transform(Empresa $model) {
        return [
            'id' => (int) $model->getKey(),
            'descripcion' => (string) $model->Nombre . ' (' .  $model->AliasBDD . ')',
            'nombre' => (string) $model->Nombre,
            'alias_bdd' => (string) $model->AliasBDD
        ];
    }
}