<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\ContraRecibo;
use League\Fractal\TransformerAbstract;

class ContraReciboTransformer extends TransformerAbstract
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

    public function transform(ContraRecibo $model)
    {
        return [
            'numero_folio' => $model->numero_folio,
            'observaciones' => $model->observaciones,
            'numero_folio_format' => $model->numero_folio_format,
        ];
    }
}
