<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:54
 */

namespace App\Http\Transformers\CADECO\Seguridad;

use App\Models\CADECO\Seguridad\AuditoriaPermisoRol;
use League\Fractal\TransformerAbstract;


class AuditoriaPermisoRolTransformer extends TransformerAbstract
{
    public function transform(AuditoriaPermisoRol $model) {
        return [
            'id' => (int) $model->getKey(),
            'usuario_registro' => (string) $model->usuario_registro,
            'action' => (string) $model->action
        ];
    }
}