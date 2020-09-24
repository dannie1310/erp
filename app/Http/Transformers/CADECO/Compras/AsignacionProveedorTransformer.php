<?php


namespace App\Http\Transformers\CADECO\Compras;

use League\Fractal\TransformerAbstract;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Http\Transformers\CADECO\Compras\AsignacionProveedorPartidaTransformer;

class AsignacionProveedorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas',
        'solicitud_compra',
        'solicitud',
        'usuario',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'usuario',
        'solicitud'
    ];

    public function transform(AsignacionProveedor $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->solicitud->fecha,
            'id_solicitud' => $model->solicitud->id_transaccion,
            'fecha_solicitud_format' => $model->solicitud->fecha_format,
            'fecha_asignacion' => date('d/m/Y H:i', strtotime($model->timestamp_registro)),
            'fecha_format' => $model->fecha_format,
            'concepto' => (string) $model->solicitud->complemento?$model->solicitud->complemento->concepto:'',
            'estado_format' => $model->estadoAsignacion->descripcion,
            'estado' => $model->estado,
            'folio_solicitud_format' => $model->solicitud->numero_folio_format,
            'opciones' => $model->solicitud->opciones,
            'folio_asignacion_format' => $model->folio_format,
            'aplicada' => $model->aplicada,

        ];
    }

    /**
     * @param AsignacionProveedor $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePartidas(AsignacionProveedor $model)
    {
        if ($partidas = $model->partidas) {
            return $this->collection($partidas, new AsignacionProveedorPartidaTransformer);
        }
        return null;
    }

    /**
     * @param AsignacionProveedor $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSolicitudCompra(AsignacionProveedor $model)
    {
        if ($solicitud = $model->solicitud) {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
    }

    /**
     * @param AsignacionProveedor $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(AsignacionProveedor $model)
    {
        if($usuario = $model->usuarioRegistro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param AsignacionProveedor $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(AsignacionProveedor $model)
    {
        if($solicitud = $model->solicitud)
        {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
        return null;
    }
}
