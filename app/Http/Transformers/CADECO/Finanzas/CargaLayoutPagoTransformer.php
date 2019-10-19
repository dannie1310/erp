<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 04:01 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\CADECO\Finanzas\CtgEstadoLayoutPagoTransformer;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Models\IGH\Usuario;
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
        'usuario',
        'usuario_autorizo',
        'estado',
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
            'monto'=> '$ '.number_format($model->monto_layout_pagos, 2, '.', ''),
            'fecha_registro' => date('d/m/Y', strtotime($model->fecha_hora_carga)),
            'fecha_autorizacion'=>date('Y-m-d H:i:s', strtotime($model->fecha_hora_autorizado))
        ];
    }

    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeEstado(LayoutPago $model)
    {
        if($estado = $model->estadoLayout){
            return $this->item($estado, new CtgEstadoLayoutPagoTransformer);
        }
        return null;
    }

    /**
     * @param LayoutPago $model
     * @return  \League\Fractal\Resource\Collection|null
     */

    public function includePartidas(LayoutPago $model)
    {
        if($partidas = $model->partidas){
            return  $this->collection($partidas, new LayoutPagoPartidaTransformer);
        }
        return null;
    }


    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeUsuario(LayoutPago $model)
    {
        if($usuario = $model->usuario){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;

    }

    /**
     * @param LayoutPago $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeUsuarioAutorizo(LayoutPago $model)
    {
        if($usuario_autorizo = $model->usuarioAutorizo){
            return $this->item($usuario_autorizo, new UsuarioTransformer);
        }
        return null;

    }
}
