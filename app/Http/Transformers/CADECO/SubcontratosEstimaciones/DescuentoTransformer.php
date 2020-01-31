<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 12:33 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\Material;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;


class DescuentoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'material',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Descuento $model)
    {
        return [
            'id' => $model->getKey(),
            'id_transaccion' => $model->id_transaccion,
            'id_material'=> $model->id_material,
            'cantidad'=>$model->cantidad,
            'cantidad_format'=>$model->cantidad_format,
            'precio'=>$model->precio,
            'precio_format'=>$model->precio_format,
            'importe'=>$model->importe,
            'importe_format'=>$model->importe_format,
            'creado'=>$model->creado,
        ];
    }

    /**
     * @param Descuento $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeMaterial(Descuento $model){
        if($material = $model->material){
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}