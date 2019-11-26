<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:20 PM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\NuevoLote;
use League\Fractal\TransformerAbstract;

class NuevoLoteTransformer extends TransformerAbstract
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

    public function transform(NuevoLote $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'referencia' => $model->referencia,
            'estado_format' => $model->estatus,
            'tipo' => $model->tipo,
        ];
    }

    /**
     * @param NuevoLote $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(NuevoLote $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    /**
     * @param NuevoLote $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(NuevoLote $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new NuevoLotePartidaTransformer);
        }
        return null;
    }

    /**
     * @param NuevoLote $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(NuevoLote $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}