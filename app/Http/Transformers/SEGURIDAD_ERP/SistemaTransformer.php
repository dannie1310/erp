<?php

/**
 * Created by PhpStorm.
 * User: Hermes
 * Date: 26/02/2019
 * Time: 06:13 PM
 */
namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Sistema;
use League\Fractal\TransformerAbstract;

class SistemaTransformer extends TransformerAbstract
{

    public function transform(Sistema $model) {
        return [
            'id' => (int) $model->getKey(),
            'name' => (string) $model->name,
            'description' => (string) $model->description,
            'url' => (string) $model->url,
            'icon' => (string) $model->icon,
            'color' => (string) $model->color,
            'externo' => (bool) $model->externo,
            'manual' => (string) $model->url_manual
        ];
    }

}
