<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Models\CADECO\ItemComprobanteFondo;
use League\Fractal\TransformerAbstract;

class ComprobanteFondoPartidaTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    /**
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(ItemComprobanteFondo $model)
    {
        return [
            'id' => $model->getKey(),
            'cantidad' => $model->cantidad,
            'importe' => $model->importe,
            'cantidad_format' => $model->cantidad_format,
            'importe_format' => $model->importe_format,
            'referencia' => $model->referencia,
            'monto' => $model->monto,
            'monto_format' => $model->monto_format
        ];
    }

    /**
     * @param ItemComprobanteFondo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(ItemComprobanteFondo $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }
}
