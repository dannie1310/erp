<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:50
 */

namespace App\Http\Transformers\CADECO\Seguridad;

use App\Models\CADECO\Seguridad\AuditoriaRolUser;
use League\Fractal\TransformerAbstract;



class AuditoriaRolUserTransformer extends TransformerAbstract
{
    public function transform(AuditoriaRolUser $model) {
        return [
            'id' => (int) $model->getKey(),
            'usuario_registro' => (string) $model->usuario_registro,
            'action' => (string) $model->action
        ];
    }
}