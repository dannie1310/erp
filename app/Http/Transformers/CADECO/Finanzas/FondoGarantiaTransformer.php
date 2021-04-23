<?php


namespace App\Http\Transformers\CADECO\Finanzas;

use App\Models\CADECO\FondoGarantia;
use League\Fractal\TransformerAbstract;

class FondoGarantiaTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(FondoGarantia $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => (string)$model->fecha_format,
            'tipo_transaccion' => $model->tipo_transaccion,
            'numero_folio_format'=>(string)$model->numero_folio_format,
            'folio_revision_format'=>(string)$model->numero_folio_revision,
            'monto_revision_format' => $model->monto_revision_format,
            'monto_revision'=>$model->saldo_pesos,
            'tipo_cambio' => $model->tipo_cambio,
            'monto_format'=>$model->monto_format,
            'monto_pesos'=>$model->monto_pesos,
            'subtotal'=>$model->monto_pesos,
            'subtotal_format'=>$model->monto_pesosformat,
            'referencia'=>$model->referencia,
            'referencia_revision'=>$model->referencia_revision,
            'observaciones'=>$model->observaciones,
            'seleccionado' => false,

        ];
    }
}