<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/11/2019
 * Time: 06:21 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\RequisicionPartida;
use League\Fractal\TransformerAbstract;

class RequisicionPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'complemento',
        'material'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(RequisicionPartida $model)
    {
        return [
            'id' => $model->getKey(),
            'unidad' => $model->unidad,
            'cantidad' => $model->cantidad_format
        ];
    }

    /**
     * @param RequisicionPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeComplemento(RequisicionPartida $model)
    {
        if($complemento = $model->complemento)
        {
            return $this->item($complemento, new RequisicionPartidaComplementoTransformer);
        }
        return null;
    }

    /**
     * @param RequisicionPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(RequisicionPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }
}