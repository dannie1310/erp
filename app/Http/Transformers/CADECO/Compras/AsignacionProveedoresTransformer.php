<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Models\CADECO\SolicitudCompra;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedores;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Http\Transformers\CADECO\Compras\AsignacionProveedoresPartidaTransformer;

class AsignacionProveedoresTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas',
        'solicitud_compra',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(AsignacionProveedores $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->solicitud->fecha,
            'fecha_solicitud_format' => $model->solicitud->fecha_format,
            'fecha_format' => $model->fecha_format,
            'observaciones' => (string) $model->solicitud->observaciones,
            'estado' => $model->estadoAsignacion->descripcion,
            'folio_solicitud_format' => $model->solicitud->numero_folio_format,
            'opciones' => $model->solicitud->opciones,
            'folio_asignacion_format' => $model->folio_format,
        ];
    }

    /**
     * @param AsignacionProveedoresPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includePartidas(AsignacionProveedores $model)
    {
        if ($partidas = $model->partidas) {
            return $this->collection($partidas, new AsignacionProveedoresPartidaTransformer);
        }
        return null;
    }

    /**
     * @param SolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitudCompra(AsignacionProveedores $model)
    {
        if ($solicitud = $model->solicitud) {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
        return null;
    }
}
