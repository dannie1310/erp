<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;

use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\SEGURIDAD_ERP\Finanzas\TransaccionesEfos;
use League\Fractal\TransformerAbstract;

class TransaccionesEfosTransformer extends TransformerAbstract
{


    public function transform(TransaccionesEfos $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'numero_folio' => $model->numero_folio_format,
            'fecha' => $model->fecha_format,
            'referencia' => $model->observaciones,
            'tipo_transaccion' => $model->tipo->Descripcion,
            'razon_social' => $model->empresa->razon_social,
            'efo' => $model->empresa->efo
        ];
    }
}