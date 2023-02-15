<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ContactoProveedorREP;
use League\Fractal\TransformerAbstract;

class ContactoProveedorTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
    ];

    protected $defaultIncludes = [

    ];

    public function transform(ContactoProveedorREP $model)
    {
        return [
            'id' => $model->getKey(),
            'correo' => $model->correo,
            'nombre' => mb_strtoupper($model->nombre),

        ];
    }


}
