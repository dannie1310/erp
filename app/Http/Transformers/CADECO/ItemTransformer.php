<?php


namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Item;
use App\Http\Transformers\CADECO\ConceptoTransformer;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
             'concepto',
             'contrato',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];


    public function transform(Item $model)
    {
        return [
          'id' => $model->getKey(),
          'id_transaccion'=> $model->id_transacccion,
          'id_antecedente' => $model->id_antecedente,
          'item_antecedente' => $model->item_antecedente,
          'id_concepto' => $model->id_concepto,
          'cantidad' => $model->cantidad,
          'precio_unitario' => $model->precio_unitario,
          'cantidad_format' => $model->cantidad_format,
          'precio_unitario_format' => $model->precio_unitario_format,
          'estado'=> $model->estado

        ];
    }

    /**
     * @param Item $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto(Item $model)
    {
        if($concepto = $model->concepto) {
            return $this->item($concepto, new ConceptoTransformer);

        }
        return null;
    }

    /**
     * @param Item $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeContrato(Item $model)
    {
        if($contrato = $model->contrato) {
            return $this->item($contrato, new ContratoTransformer);
        }
        return null;
    }
}
