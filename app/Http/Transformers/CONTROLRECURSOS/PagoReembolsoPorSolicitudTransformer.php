<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\PagoReembolsoPorSolicitud;
use League\Fractal\TransformerAbstract;

class PagoReembolsoPorSolicitudTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentaProveedor'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'empresa',
        'proveedor',
    ];

    public function transform(PagoReembolsoPorSolicitud $model){
        return [
            'id' => $model->getKey(),
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'fecha_vencimiento_format' => $model->fecha_vencimiento_format,
            'importe' => $model->Importe,
            'importe_format' => $model->importe_format,
            'iva' => $model->IVA,
            'total' => $model->Total,
            'serie' => $model->Serie,
            'folio' => $model->Folio,
            'moneda' => $model->moneda_descripcion,
            'idempresa' => $model->IdEmpresa,
            'idproveedor' => $model->IdProveedor,
            'idcuentaempresa' => '',
            'total_format' => $model->total_format,
            'id_solicitante' => $model->firma_solicitante,
            'id_relacion' => $model->id_relacion,
            'id_forma_pago' => $model->IdFormaPago,
            'id_entrega' => $model->IdEntrega,
        ];
    }


    /**
     * @param PagoReembolsoPorSolicitud $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(PagoReembolsoPorSolicitud $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorTransformer);
        }
        return null;
    }

    /**
     * @param PagoReembolsoPorSolicitud $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(PagoReembolsoPorSolicitud $model)
    {
        if($empresa =  $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param PagoReembolsoPorSolicitud $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaProveedor(PagoReembolsoPorSolicitud $model)
    {
        if($cuenta  = $model->cuentaProveedor)
        {
            return $this->item($cuenta, new CuentaProveedorTransformer);
        }
        return null;
    }
}
