<?php


namespace App\Http\Transformers\CADECO\Contrato;


use App\Models\CADECO\ItemAvanceSubcontrato;
use League\Fractal\TransformerAbstract;

class ItemAvanceSubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(ItemAvanceSubcontrato $model)
    {
        return $model->toArray();
    }
}
