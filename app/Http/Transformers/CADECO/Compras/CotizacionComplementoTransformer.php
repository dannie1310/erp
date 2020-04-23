<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Models\CADECO\Compras\CotizacionComplemento;
use League\Fractal\TransformerAbstract;

class CotizacionComplementoTransformer extends TransformerAbstract
{
    public function transform(CotizacionComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'descuento' => $model->descuento_format,
            'anticipo' => $model->anticipo_format,
            'parcialidades' => $model->parcialidades_format,
            'dias_credito' => (int) $model->dias_credito,
            'vigencia' => (int) $model->vigencia,
            'entrega' => (int) $model->plazo_entrega,
            'tc_usd' => $model->tipo_cambio_usd_format,
            'tc_eur' => $model->tipo_cambio_eur_format
        ];
    }
}