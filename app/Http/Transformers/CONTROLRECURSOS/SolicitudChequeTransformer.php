<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolCheque;
use League\Fractal\TransformerAbstract;

class SolicitudChequeTransformer extends TransformerAbstract
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

    public function transform(SolCheque $model){
        return [
            'id' => $model->getKey(),
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'importe' => $model->Importe,
            'importe_format' => $model->importe_format,
            'iva' => $model->IVA,
            'total' => $model->Total,
            'serie' => $model->Serie,
            'folio' => $model->Folio,
            'moneda' => $model->moneda_descripcion,
            'idempresa' => $model->IdEmpresa,
            'idproveedor' => $model->IdProveedor,
            'idcuentaempresa' => ''
        ];
    }

    /**
     * @param SolCheque $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(SolCheque $model)
    {
        if($empresa =  $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param SolCheque $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(SolCheque $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorTransformer);
        }
        return null;
    }

    /**
     * @param SolCheque $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaProveedor(SolCheque $model)
    {
        if($cuenta  = $model->cuentaProveedor)
        {
            return $this->item($cuenta, new CuentaProveedorTransformer);
        }
        return null;
    }
}
