<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 01:43 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEfosTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\EfoTransformer;
use App\Models\CADECO\Cliente;
use League\Fractal\TransformerAbstract;

class ClienteTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario_registro'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'efo'
    ];

    public function transform(Cliente $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc'=> $model->rfc,
            'tipo' => $model->tipo,
            'tipo_cliente' => (int) $model->tipo_cliente,
            'porcentaje' =>  $model->porcentaje_format,
            'porcentaje_format' => $model->porcentaje_con_signo_format,
            'fecha_registro' => $model->FechaHoraRegistro,
            'fecha_registro_format' => $model->fecha_hora_registro_format

        ];
    }

    /**
     * @param Cliente $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEfo(Cliente $model)
    {
        if($efo = $model->efo)
        {
            return $this->item($efo, new CtgEfosTransformer);
        }
        return null;
    }

    /**
     * @param Cliente $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuarioRegistro(Cliente $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}
