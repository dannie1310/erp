<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 11:54 AM
 */

namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\CADECO\AlmacenTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\AjusteNegativo;
use League\Fractal\TransformerAbstract;

class AjusteNegativoTransformer extends TransformerAbstract
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

    public function transform(AjusteNegativo $model)
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
     * @param AjusteNegativo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAlmacen(AjusteNegativo $model)
    {
        if($almacen = $model->almacen)
        {
            return $this->item($almacen, new AlmacenTransformer);
        }
        return null;
    }

    /**
     * @param AjusteNegativo $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(AjusteNegativo $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new AjusteNegativoPartidaTransformer);
        }
        return null;
    }

    /**
     * @param AjusteNegativo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(AjusteNegativo $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}