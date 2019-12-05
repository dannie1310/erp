<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\Compras\ItemContratista;
use League\Fractal\TransformerAbstract;

class ItemContratistaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'empresa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'empresa'
    ];

    /**
     * @param ItemContratista $model
     * @return array
     */
    public function transform(ItemContratista $model)
    {
        return [
            'id_item' => (int)$model->getKey(),
            'con_cargo' => $model->con_cargo,
        ];
    }

    /**
     * @param ItemContratista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(ItemContratista $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

}