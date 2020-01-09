<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 01:43 PM
 */

namespace App\Http\Transformers\CADECO;


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
            'porcentaje' => $model->porcentaje,
            'porcentaje_format' => $model->porcentaje_format
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
            return $this->item($efo, new EfoTransformer);
        }
        return null;
    }
}