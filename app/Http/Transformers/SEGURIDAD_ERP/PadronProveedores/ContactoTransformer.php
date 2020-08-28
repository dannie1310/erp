<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Contacto;
use League\Fractal\TransformerAbstract;

class ContactoTransformer extends TransformerAbstract
{
    public function transform(Contacto $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre' => $model->nombre,
            'correo_electronico' => $model->correo_electronico,
            'telefono' => $model->telefono,
            'puesto' => $model->puesto,
            'notas' => $model->notas,
        ];
    }
}
