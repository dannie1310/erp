<?php

namespace App\Http\Transformers\IGH;


use App\Models\IGH\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    public function transform($model) {
        return [

            'color' => $model->color,
            'icon' => $model->icon,
            'menu' => ($model->menu)?$model->menu:$model->name,
            'ruta' => ($model->ruta)?$model->ruta:$model->url,
            'target' => $model->target,
            'manual' => (string) $model->url_manual
        ];
    }
}
