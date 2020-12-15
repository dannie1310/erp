<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Models\CADECO\SubcontratosCM\Item;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\ItemTransformer as ItemSubcontratoTransformer;

class ItemTransformer extends TransformerAbstract
{
    /**
     * @var string[]
     */
    protected $availableIncludes = [
        'tipo',
        'item_subcontrato',
        'concepto'
    ];

    public function transform(Item $model)
    {
        return [
            'id' => $model->getKey(),
            'cantidad' => $model->cantidad,
            'precio' => $model->precio,
            'importe' => $model->importe,
        ];
    }

    public function includeTipo(Item $model) {
        if ($tipo = $model->tipo) {
            return $this->item($tipo, new CtgTipoTransformer);
        }
        return null;
    }

    public function includeItemSubcontrato(Item $model) {
        if ($itemSubcontrato = $model->itemSubcontrato) {
            return $this->item($itemSubcontrato, new ItemSubcontratoTransformer);
        }
        return null;
    }

    public function includeConcepto(Item $model) {
        if ($concepto = $model->concepto) {
            return $this->item($concepto, new ContratoTransformer);
        }
        return null;
    }
}
