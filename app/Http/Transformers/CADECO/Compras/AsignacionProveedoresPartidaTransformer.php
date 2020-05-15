<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Http\Transformers\CADECO\CotizacionCompraPartidaTransformer;
use App\Http\Transformers\CADECO\CotizacionCompraTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\Compras\AsignacionProveedores;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;

class AsignacionProveedoresPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cotizacion_compra',
        'cotizacion',
        'material'
    ];

    public function transform(AsignacionProveedoresPartida $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'cantidad_asignada' => $model->cantidad_asignada,
            'cantidad_asignada_format' => $model->cantidad_asignada_format
        ];
    }

    /**
     * Include CotizacionCompra
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacionCompra(AsignacionProveedoresPartida $model)
    {
        if($cotizacion = $model->cotizacionCompra)
        {
            return $this->item($cotizacion, new CotizacionCompraTransformer);
        }
        return null;
    }

    /**
     * Include Material
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(AsignacionProveedoresPartida $model)
    {
        if($material = $model->material)
        {
            return $this->item($material, new MaterialTransformer);
        }
    }

    /**
     * Include Cotizacion
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacion(AsignacionProveedoresPartida $model)
    {
        if($cotizaciones = $model->cotizacion)
        {
            return $this->item($cotizaciones, new CotizacionCompraPartidaTransformer);
        }
        return null;
    }
}
