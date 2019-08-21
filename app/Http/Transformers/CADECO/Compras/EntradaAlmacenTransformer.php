<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 12:58 PM
 */

namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\EntradaMaterial;
use League\Fractal\TransformerAbstract;

class EntradaAlmacenTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'partidas'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(EntradaMaterial $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_registro' => $model->fechaHoraRegistroFormat,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'estado_format' => $model->estadoFormat,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format_orden,
            'referencia' => $model->referencia,
        ];
    }

    /**
     * @param EntradaMaterial $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(EntradaMaterial $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param EntradaMaterial $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(EntradaMaterial $model)
    {
        if($partida = $model->partidas)
        {
            return $this->collection($partida, new EntradaAlmacenPartidaTransformer);
        }
        return null;
    }
}