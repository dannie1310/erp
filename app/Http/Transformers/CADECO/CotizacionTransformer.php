<?php


namespace App\Http\Transformers\CADECO;

use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Transaccion;
use League\Fractal\TransformerAbstract;

class CotizacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     * 
     * @var array
     */
    protected $availableIncludes = [
        'solicitud',
        'empresa',
        'sucursal'
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
            'folio' => $model->numero_folio,
            'operacion' => $model->operacion,
            'opciones' => $model->opciones,
            'folio_format' => $model->numero_folio_format,
            'usuario_registro' => ($model->id_usuario) ? $model->id_usuario : '--------------'
        ];
    }

    /**
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
     * @param OrdenCompra $model
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
     * Include Sucursal
     *
     * @param OrdenCompra $model
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
}