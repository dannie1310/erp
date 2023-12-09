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
        'empresa',
        'proveedor',
    ];

    public function transform(PagoAProveedor $model){
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
            'idcuentaempresa' => '',
            'total_format' => $model->total_format
        ];
    }
}
