<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;

use App\Models\SEGURIDAD_ERP\Permiso;
use League\Fractal\TransformerAbstract;

class PermisoTransformer extends TransformerAbstract
{
    public function transform(Permiso $model) {
        return [
            'id' => (int) $model->getKey(),
            'description' => (string) $model->description,
            'display_name' => (string) $model->display_name,
            'name' => (string) $model->name
        ];
    }

}