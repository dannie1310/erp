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
use App\Http\Transformers\CADECO\Almacenes\EntradaAlmacenTransaccionesRelacionadasTransformer;

class EntradaAlmacenTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'empresa',
        'partidas',
        'orden_compra',
        'transacciones_relacionadas'
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
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'estado' => $model->estado,
            'estado_format' => $model->estadoFormat,
            'folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'referencia' => $model->referencia,
            'empresa_razon_social' => $model->empresa ? $model->empresa->razon_social : '',
            'orden_compra_numero_folio_format' => $model->ordenCompra->numero_folio_format,
            'solicitud_numero_folio_format' => $model->ordenCompra->solicitud->numero_folio_format,
        ];
    }


    /**
     * @param EntradaMaterial $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(EntradaMaterial $model)
    {
        if ($empresa = $model->empresa) {
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
        if ($partida = $model->partidas) {
            return $this->collection($partida, new EntradaAlmacenPartidaTransformer);
        }
        return null;
    }

    /**
     * @param EntradaMaterial $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeOrdenCompra(EntradaMaterial $model)
    {
        if ($orden = $model->ordenCompra) {
            return $this->item($orden, new OrdenCompraTransformer);
        }
        return null;
    }

    public function includeTransaccionesRelacionadas(EntradaMaterial $model)
    {
        if ($partida = $model->transacciones_relacionadas) {
            return $this->item($partida, new EntradaAlmacenTransaccionesRelacionadasTransformer());
        }
        return null;
    }
}
