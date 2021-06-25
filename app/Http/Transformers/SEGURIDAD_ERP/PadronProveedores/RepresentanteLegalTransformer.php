<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\PadronProveedores\RepresentanteLegal;

class RepresentanteLegalTransformer extends TransformerAbstract
{
    public function transform(RepresentanteLegal $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre' => $model->nombre,
            'apellido_paterno' => $model->apellido_paterno,
            'apellido_materno' => $model->apellido_materno,
            'curp' => $model->curp,
            'es_nacional' => $model->es_nacional
        ];
    }
}
