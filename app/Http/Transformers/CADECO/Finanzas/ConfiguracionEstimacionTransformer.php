<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use League\Fractal\TransformerAbstract;

class ConfiguracionEstimacionTransformer extends TransformerAbstract
{
    public function transform(ConfiguracionEstimacion $model){
        return $model->toArray();
    }

}