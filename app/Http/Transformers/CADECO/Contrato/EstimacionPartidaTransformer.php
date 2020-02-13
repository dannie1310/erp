<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/10/2019
 * Time: 07:30 PM
 */

namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Models\CADECO\ItemEstimacion;
use League\Fractal\TransformerAbstract;

class EstimacionPartidaTransformer extends TransformerAbstract
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


    public function transform(ItemEstimacion $model)
    {
        return [
            'id' => $model->getKey(),
            'id_antecedente' => $model->id_antecedente,
            'item_antecedente' => $model->item_antecedente,
            'id_concepto' => $model->id_concepto,
            'cantidad' => $model->cantidad,
            'precio_unitario' => $model->precio_unitario,
            'estado'=> $model->estado,
            'cantidad_format' => $model->cantidad_format,
            'precio_unitario_format' => $model->precio_unitario_format,

        ];
    }

    /**
     * @param EstimacionPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(ItemEstimacion $model)
    {
        if($concepto = $model->concepto) {
            return $this->item($concepto, new ConceptoTransformer);

        }
        return null;
    }
}
