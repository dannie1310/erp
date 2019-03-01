<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/03/2019
 * Time: 11:39 AM
 */

namespace App\Http\Transformers\IGH;


use App\Models\IGH\Usuario;
use League\Fractal\TransformerAbstract;

class UsuarioTransformer extends TransformerAbstract
{
    public function transform(Usuario $model) {
        dd($model);
        return [
            'id' => (int) $model->getKey(),
            'nombre' => $model->nombre." ".$model->apaterno." ".$model->amaterno
        ];
    }

}