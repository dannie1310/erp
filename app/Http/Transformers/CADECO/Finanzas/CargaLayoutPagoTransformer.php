<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Finanzas\LayoutPago;
use League\Fractal\TransformerAbstract;

class CargaLayoutPagoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas',
        'estado',
        'usuario',
        'usuario_autorizo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(LayoutPago $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_hora_carga' => date('Y-m-d H:i', strtotime($model->fecha_hora_carga)),
            'monto_layout_pagos' => '$ ' . number_format($model->monto_layout_pagos,2),
            'nombre_layout_pagos' => $model->nombre_layout_pagos,
            'fecha_hora_autorizo' =>$model->fecha_hora_autorizo ? date('Y-m-d H:i', strtotime($model->fecha_hora_carga)): null
        ];
    }

    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEstado(LayoutPago $model){
        if($estado = $model->estadoLayout){
            return $this->item($estado, new CtgEstadoLayoutPagoTransformer);
        }
        return null;
    }
    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(LayoutPago $model){
        if($partidas = $model->partidas){
            return $this->collection($partidas, new LayoutPagoPartidaTransformer);
        }
        return null;
    }

    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(LayoutPago $model){
        if($usuario = $model->usuario){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuarioAutorizo(LayoutPago $model){
        if($usuario_autorizo = $model->usuarioAutorizo){
            return $this->item($usuario_autorizo, new UsuarioTransformer);
        }
        return null;
    }
}