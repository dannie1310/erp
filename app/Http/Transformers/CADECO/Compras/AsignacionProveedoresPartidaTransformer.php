<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Http\Transformers\CADECO\CotizacionCompraPartidaTransformer;
use App\Http\Transformers\CADECO\CotizacionCompraTransformer;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\Compras\AsignacionProveedores;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\MaterialTransformer;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;
use App\Http\Transformers\CADECO\CotizacionCompraTransformer;
use App\Http\Transformers\CADECO\CotizacionCompraPartidaTransformer;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraPartidaTransformer;

class AsignacionProveedoresPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'item_solicitud',
        'cotizacion_partida',
        'cotizacion',
        'material',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

        'cotizacion_compra',
        'cotizacion',
        'material'
    ];

    public function transform(AsignacionProveedoresPartida $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'cantidad' => $model->cantidad_asignada,
            'cantidad_format' => $model->cantidad_format,
            'item' => $model->id_item_solicitud,
            'cotizacion' => $model->id_transaccion_cotizacion,
            'cantidad_asignada' => $model->cantidad_asignada,
            'cantidad_asignada_format' => $model->cantidad_asignada_format
        ];
    }

    /**
     * @param ItemSolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeItemSolicitud(AsignacionProveedoresPartida $model)
    {
        
        if ($itemSolicitud = $model->itemSolicitud) {
            return $this->item($itemSolicitud, new SolicitudCompraPartidaTransformer);
        }
        return null;
    }
    
    /**
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacion(AsignacionProveedoresPartida $model)
    {
        if ($cotizacion = $model->cotizacionCompra) {
            return $this->item($cotizacion, new CotizacionCompraTransformer);
        }
        return null;
    }
    
    /**
     * @param CotizacionCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacionPartida(AsignacionProveedoresPartida $model)
    {
        if ($cotizacion = $model->cotizacion) {
            return $this->item($cotizacion, new CotizacionCompraPartidaTransformer);
        }
        return null;
    }
    
    /**
     * @param Material $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMaterial(AsignacionProveedoresPartida $model)
    {
        if ($material = $model->material) {
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
