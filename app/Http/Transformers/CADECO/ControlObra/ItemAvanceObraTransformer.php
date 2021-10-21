<?php


namespace App\Http\Transformers\CADECO\ControlObra;


use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Models\CADECO\ItemAvanceObra;
use League\Fractal\TransformerAbstract;

class ItemAvanceObraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(ItemAvanceObra $model)
    {
        return [
            'id' => $model->getKey(),
            'cantidad' => $model->cantidad,
            'precio_unitario' => $model->precio_unitario,
            'cantidad_format' => $model->cantidad_format,
            'precio_unitario_format' => $model->precio_unitario_format,
            'importe_format' => $model->importe_format,
            'cantidad_avance_actual' => $model->cantidad_avance_actual,
            'cantidad_avance_actual_format' => $model->cantidad_avance_actual_format,
            'monto_avance_actual' => $model->monto_avance_actual,
            'monto_avance_actual_format' => $model->monto_avance_actual_format,
            'cantidad_anterior_avance' => $model->cantidad_anterior_avance_format,
            'monto_avance' => $model->monto_avance,
            'monto_avance_format' => $model->monto_avance_format
        ];
    }

    /**
     * @param ItemAvanceObra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(ItemAvanceObra $model)
    {
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }
}
