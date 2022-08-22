<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/02/2019
 * Time: 02:13 PM
 */

namespace App\Http\Transformers\IGH;

use App\Models\IGH\Usuario;
use League\Fractal\TransformerAbstract;

class UsuarioTransformer extends TransformerAbstract
{
    public function transform(Usuario $model) {

        return [
            'id' => (int) $model->getKey(),
            'nombre' => $model->getNombreCompletoAttribute(),
            'usuario' => $model->usuario,
            'idUbicacion' => $model->idubicacion
        ];
    }
}
