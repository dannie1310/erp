<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\Marbete;
use League\Fractal\TransformerAbstract;

class MarbeteTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'inventario_fisico'
    ];

    public function transform(Marbete $model)
    {
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio,
            'folio_marbete' => $model->folio_marbete,
            'id_inventario_fisico' => $model->id_inventario_fisico
        ];
    }

    public function includeInventarioFisico(Marbete $model){
        if($invetarioFisico = $model->invetarioFisico){
            return $this->item($invetarioFisico, new InventarioFisicoTransformer);
        }
        return null;
    }

}