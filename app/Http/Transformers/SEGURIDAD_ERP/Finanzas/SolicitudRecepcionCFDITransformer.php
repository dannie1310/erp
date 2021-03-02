<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use League\Fractal\TransformerAbstract;

class SolicitudRecepcionCFDITransformer extends TransformerAbstract
{
    public function transform(SolicitudRecepcionCFDI $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'fecha_registro' => $model->fecha_hora_registro,
        ];
    }

}
