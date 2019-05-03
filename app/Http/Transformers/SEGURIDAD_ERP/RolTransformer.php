<?php

namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Rol;
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