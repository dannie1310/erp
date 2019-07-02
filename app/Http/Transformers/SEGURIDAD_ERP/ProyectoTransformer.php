<?php


namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Proyecto;
use League\Fractal\TransformerAbstract;

class ProyectoTransformer extends TransformerAbstract
{
    public function transform(Proyecto $model) {
        return [
            'id' => (int) $model->getKey(),
            'base_datos' => (string) $model->base_datos,
            'id_obras' =>$model->id_obra,
            'nombre_obra' =>(string)$model->nombre,
        ];
    }

}