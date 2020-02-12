<?php


namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Models\CADECO\ItemSubcontrato;
use League\Fractal\TransformerAbstract;

class SubcontratoPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'contrato_conceptos'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    /**
     * @param ItemSubcontrato $model
     * @return array
     */
    public function transform(ItemSubcontrato $model)
    {
        return [
            'id' => (int)$model->getKey(),
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
     * @param ItemSubcontrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeContratoConceptos(ItemSubcontrato $model)
    {
        if($contrato = $model->contrato)
        {
            return $this->item($contrato, new ContratoTransformer);
        }
        return null;
    }
}
