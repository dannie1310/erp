<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Models\CADECO\OrdenCompra;
use League\Fractal\TransformerAbstract;

class OrdenCompraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(OrdenCompra $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'numero_folio_format' => (string)$model->numero_folio_format,
            'observaciones' => (string)$model->observaciones,
            'empresa_sucursal'=> (string)$model->empresa->razon_social .' / '. (string)$model->sucursal->descripcion,
        ];
    }


    /**
     * @param OrdenCompra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(OrdenCompra $model)
    {
        if($partidas = $model->partidas()->DisponibleEntradaAlmacen()->get()){
            return $this->collection($partidas, new OrdenCompraPartidaTransformer);
        }
        return null;
    }

}