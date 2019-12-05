<?php

namespace App\Http\Transformers\IGH;


use App\Models\IGH\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    public function transform(Menu $model) {
        $model_arr = $model->toArray();
        $model_arr["target"] = $model->target;
        return $model_arr;
    }
}