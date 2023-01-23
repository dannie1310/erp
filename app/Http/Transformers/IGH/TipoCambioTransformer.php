<?php


namespace App\Http\Transformers\IGH;


use App\Models\IGH\TipoCambio;
use League\Fractal\TransformerAbstract;

class TipoCambioTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];


    public function transform(TipoCambio $model)
    {

        return [
            'id' => (int)$model->getKey(),
            'fecha'=>(string)$model->fecha,
            'moneda' => $model->moneda,
            'tipo_cambio' => $model->tipo_cambio
        ];
    }

}
