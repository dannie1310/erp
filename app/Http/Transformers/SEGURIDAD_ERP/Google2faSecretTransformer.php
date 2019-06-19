<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Google2faSecret;
use League\Fractal\TransformerAbstract;

class Google2faSecretTransformer extends TransformerAbstract
{
    public function transform(Google2faSecret $model)
    {
        return $model->toArray();
    }
}