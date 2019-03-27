<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 26/03/2019
 * Time: 20:07
 */

namespace App\Http\Transformers\CADECO\Seguridad;

use App\Models\CADECO\Seguridad\Rol;
use League\Fractal\TransformerAbstract;



class RolTransformer extends TransformerAbstract
{
    public function transform(Rol $model) {
        return [
            'id' => (int) $model->getKey(),
            'description' => (string) $model->description,
            'display_name' => (string) $model->display_name,
            'name' => (string) $model->name
        ];
    }

}