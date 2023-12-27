<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\PagoAProveedor;
use League\Fractal\TransformerAbstract;

class PagoAProveedorTransformer extends TransformerAbstract
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
        'proveedor',
        'empresa',
    ];

    public function transform(PagoAProveedor $model){
        return [
            'id' => $model->getKey(),
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'fecha_vencimiento' => $model->fecha_vencimiento_format,
            'importe' => $model->Importe,
            'importe_format' => $model->importe_format,
            'iva' => $model->IVA,
            'iva_format' => $model->iva_format,
            'otros_format' => $model->otros_format,
            'retenciones_format' => $model->retenciones_format,
            'total' => $model->Total,
            'total_format' => $model->total_format,
            'serie' => $model->Serie,
            'folio' => $model->Folio,
            'moneda' => $model->moneda_descripcion,
            'id_empresa' => $model->IdEmpresa,
            'id_proveedor' => $model->IdProveedor,
            'folio_compuesto' => $model->folio_compuesto,
            'id_forma_pago' => $model->IdFormaPago,
            'id_entrega' => $model->IdEntrega,
            'cuenta' => $model->Cuenta2,
            'id_solicitante' => $model->firma_solicitante,
            'id_relacion' => $model->id_relacion
        ];
    }

    /**
     * @param PagoAProveedor $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(PagoAProveedor $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorTransformer);
        }
        return null;
    }

    /**
     * @param PagoAProveedor $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(PagoAProveedor $model)
    {
        if($empresa =  $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param PagoAProveedor $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaProveedor(PagoAProveedor $model)
    {
        if($cuenta  = $model->cuentaProveedor)
        {
            return $this->item($cuenta, new CuentaProveedorTransformer);
        }
        return null;
    }
}
