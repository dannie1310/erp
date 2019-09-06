<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\ConfiguracionRemesa;
use League\Fractal\TransformerAbstract;

class ConfiguracionRemesaTransformer extends TransformerAbstract
{
    public function transform(ConfiguracionRemesa $model){
        return [
            'id'=> $model->getKey(),
            'documentos_manuales'=> $model->documentos_manuales
        ];
    }

}