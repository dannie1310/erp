<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:42 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\AjustePositivo;
use League\Fractal\TransformerAbstract;

class AjustePositivoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'almacen',
        'partidas',
        'usuario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    public function transform(AjustePositivo $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format_orden,
            'referencia' => $model->referencia,
            'estado_format' => $model->estatus
        ];
    }

    /**
     * @param AjustePositivo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(AjustePositivo $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    /**
     * @param AjustePositivo $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(AjustePositivo $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new AjustePositivoPartidaTransformer);
        }
        return null;
    }

    /**
     * @param AjustePositivo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(AjustePositivo $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}