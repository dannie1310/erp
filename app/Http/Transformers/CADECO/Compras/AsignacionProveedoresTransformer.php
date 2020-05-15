<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Http\Transformers\IGH\UsuarioTransformer;
use League\Fractal\TransformerAbstract;
use App\Models\CADECO\Compras\AsignacionProveedores;

class AsignacionProveedoresTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas',
        'usuario',
        'solicitud'
    ];

    public function transform(AsignacionProveedores $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->solicitud->fecha,
            'fecha_solicitud_format' => $model->solicitud->fecha_format,
            'fecha_asignacion' => date('d/m/Y H:i', strtotime($model->timestamp_registro)),
            'fecha_format' => $model->fecha_format,
            'observaciones' => (string) $model->solicitud->observaciones,
            'estado' => $model->estadoAsignacion->descripcion,
            'folio_solicitud_format' => $model->solicitud->numero_folio_format,
            'opciones' => $model->solicitud->opciones,
            'folio_asignacion_format' => $model->folio_format,

        ];
    }

    /**
     * Include Partidas
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(AsignacionProveedores $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new AsignacionProveedoresPartidaTransformer);
        }
        return null;
    }

    /**
     * Include Usuario
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(AsignacionProveedores $model)
    {
        if($usuario = $model->usuarioRegistro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * Include Usuario
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(AsignacionProveedores $model)
    {
        if($solicitud = $model->solicitud)
        {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
        return null;
    }
}
