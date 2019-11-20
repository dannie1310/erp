<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 05:42 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Models\CADECO\Requisicion;
use App\Models\CADECO\RequisicionPartida;
use League\Fractal\TransformerAbstract;

class RequisicionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas',
        'complemento'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(Requisicion $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'fecha' => $model->fecha,
            'fecha_format'=>$model->fecha_format,
            'observaciones' => $model->observaciones,
            'numero_folio_format'=>(string)$model->numero_folio_format_orden,
        ];
    }

    /**
     * @param Requisicion $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(Requisicion $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new RequisicionPartida);
        }
        return null;
    }
}