<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/11/2019
 * Time: 06:17 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Compras\RequisicionPartidaComplemento;
use League\Fractal\TransformerAbstract;

class RequisicionPartidaComplementoTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [

    ];

    public function transform(RequisicionPartidaComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'observaciones' => $model->observaciones,
            'fecha_entrega' => $model->fecha_entrega,
            'descripcion' => $model->descripcion_material,
            'numero_parte' => $model->numero_parte,
            'unidad' => $model->unidad
        ];
    }
}