<?php

namespace App\Http\Transformers\IGH;


use App\Models\IGH\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    public function transform($model) {
        return [

            'color' => $model["color"],
            'icon' => $model["icon"],
            'menu' => $model["menu"],
            'ruta' => $model["ruta"],
            'target' => $model["target"],
            'manual' => (string) $model["manual"],
        ];
    }
}
