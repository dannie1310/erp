<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\Marbete;
use League\Fractal\TransformerAbstract;

class MarbeteTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Marbete $model)
    {
        return [
            'id'=>$model->getKey(),
            'id_inventario_fisico'=>$model->id_inventario_fisico,
            'id_almacen' =>$model->id_almacen,
            'id_material'=>$model->id_material,
            'folio'=>$model->folio,
            'saldo'=>$model->saldo,

        ];
    }


}
