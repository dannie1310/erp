<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\ArchivoIntegrante;
use League\Fractal\TransformerAbstract;

class ArchivoIntegranteTransformer extends TransformerAbstract
{
    public function transform(ArchivoIntegrante $model)
    {
        return [
            'id' => $model->getKey(),
            'nombre_archivo' => $model->nombre_archivo_usuario
        ];
    }
}
