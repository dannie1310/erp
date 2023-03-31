<?php


namespace App\Http\Transformers\CADECO\Compras;

use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\Compras\CotizacionComplementoTransaformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Models\CADECO\CotizacionCompra;
use League\Fractal\TransformerAbstract;

class CotizacionCompraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'solicitud',
        'empresa',
        'sucursal',
        'complemento',
        'partidas',
        'partidasEdicion',
        'relaciones',
        'exclusiones'
    ];

    public function transform(CotizacionCompra $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'referencia' => (string)$model->referencia,
            'observaciones' => (string)$model->observaciones,
            'estado' => (int) $model->estado,
            'estado_format' => $model->estado_format,
            'estado_formulario' => $model->estado_formulario,
            'folio' => $model->numero_folio,
            'operacion' => $model->operacion,
            'opciones' => $model->opciones,
            'folio_format' => $model->numero_folio_format,
            'numero_folio_format' => $model->numero_folio_format,
            'usuario_registro' => ($model->id_usuario) ? $model->id_usuario : '--------------',
            'importe' => $model->monto_format,
            'monto_consulta_proveedor' => $model->monto_consulta_proveedor_format,
            'impuesto_consulta_proveedor' => $model->impuesto_consulta_proveedor_format,
            'subtotal_consulta_proveedor' => $model->subtotal_consulta_proveedor_format,
            'moneda_consulta_proveedor' => $model->moneda_consulta_proveedor_format,
            'subtotal' => $model->subtotal_format,
            'impuesto' => $model->impuesto_format,
            'asignada' => $model->asignada,
            'id_referente' => $model->id_referente,
            'tasa_iva' => $model->tasa_iva,
            'tasa_iva_format' => $model->tasa_iva_format
        ];
    }

    /**
     * Include Solicitud
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitud(CotizacionCompra $model)
    {
        if($solicitud = $model->solicitud)
        {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
        return null;
    }

    /**
     * Include Empresa
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(CotizacionCompra $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * Include Complemento
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeComplemento(CotizacionCompra $model)
    {
        if($complemento = $model->complemento)
        {
            return $this->item($complemento, new CotizacionComplementoTransformer);
        }
        return null;
    }

    /**
     * Include Cotizaciones
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePartidas(CotizacionCompra $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new CotizacionCompraPartidaTransformer);
        }
        return null;
    }

    public function includePartidasEdicion(CotizacionCompra $model)
    {
        if($partidas = $model->partidasEdicion)
        {
            return $this->collection($partidas, new CotizacionCompraPartidaTransformer);
        }
        return null;
    }

    /**
     * Include Sucursal
     *
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSucursal(CotizacionCompra $model)
    {
        if($sucursal = $model->sucursal)
        {
            return $this->item($sucursal, new SucursalTransformer);
        }
        return null;
    }

    public function includeRelaciones(CotizacionCompra $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }

    /**
     * @param CotizacionCompra $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeExclusiones(CotizacionCompra $model)
    {
        if($exclusiones = $model->exclusiones)
        {
            return $this->collection($exclusiones, new ExclusionTransformer);
        }
        return null;
    }
}
