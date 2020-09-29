<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Http\Transformers\CADECO\MaterialTransformer;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraPartidaTransformer;

class AsignacionProveedorPartidaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'item_solicitud',
        'cotizacion_partida',
        'material',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'cotizacion',
        'material'
    ];

    public function transform(AsignacionProveedorPartida $model)
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
    public function includeItemSolicitud(AsignacionProveedorPartida $model)
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
    public function includeCotizacion(AsignacionProveedorPartida $model)
    {
        if ($cotizacion = $model->cotizacionCompra)
        {
            return $this->item($cotizacion, new CotizacionCompraTransformer);
        }
        return null;
    }

    /**
     * @param CotizacionCompraPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacionPartida(AsignacionProveedorPartida $model)
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
    public function includeMaterial(AsignacionProveedorPartida $model)
    {
        if ($material = $model->material) {
            return $this->item($material, new MaterialTransformer);
        }
    }
}
