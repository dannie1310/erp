<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\CADECO\Almacenes\InventarioFisicoTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
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
        'almacen',
        'material',
        'inventario_fisico',

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
            'folio_format'=>str_pad($model->folio,6,0,0),
            'folio'=>$model->folio,
            'saldo'=>$model->saldo,

        ];
    }

    /**
     * @param Marbete $model
     * @return \League\Fractal\Resource\Item
     *
     */
    public function includeAlmacen(Marbete $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    /**
     * @param Marbete $model
     * @return \League\Fractal\Resource\Item
     *
     */
    public function includeMaterial(Marbete $model)
    {

        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }

    /**
     * @param Marbete $model
     * @return \League\Fractal\Resource\Item
     *
     */

    public function includeInventarioFisico(Marbete $model)
    {

        if($inventario = $model->inventario_fisico)
        {
          return $this->item($inventario, new InventarioFisicoTransformer);
        }
        return null;
    }

}
