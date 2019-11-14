<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:33 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\Compras\SolicitudPartidaComplementoTransformer;
use App\Models\CADECO\Compras\SolicitudPartidaComplemento;
use App\Models\CADECO\SolicitudCompraPartida;
use League\Fractal\TransformerAbstract;

class SolicitudCompraPartidaTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'complemento',
        'entrega',
        'material',

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(SolicitudCompraPartida $model){
         return [
             'id' =>$model->getKey(),
             'id_transaccion' => $model->id_transaccion,
             'id_material' => $model->id_material,
             'unidad' => $model->unidad,
             'cantidad' => $model->cantidad,
             'id_concepto'=> $model->id_concepto,
             'id_almacen' =>$model->id_almacen
         ];

    }

    /**
     * @param SolicitudCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeComplemento(SolicitudCompraPartida $model)
    {
        if($complemento = $model->complemento)
        {
            return $this->item($complemento, new SolicitudPartidaComplementoTransformer);
        }
        return null;
    }


    /**
     * @param SolicitudCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeEntrega(SolicitudCompraPartida $model)
    {
        if($entrega = $model->entrega)
        {
            return $this->item($entrega, new EntregaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */

    public function includeMaterial(SolicitudCompraPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
        return null;
    }




}
