<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolChequeComprobacion;
use League\Fractal\TransformerAbstract;

class SolicitudComprobacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(SolChequeComprobacion $model){
        return [
            'id' => $model->getKey(),
            'concepto' => $model->Concepto,
            'fecha' => $model->Fecha,
            'fecha_format' => $model->fecha_format,
            'importe' => $model->Importe,
            'importe_format' => $model->importe_format,
            'comprobado' => $model->SaldoComprobado,
            'comprobado_format' => $model->comprobado_format,
            'saldo' => $model->saldo,
            'saldo_format' => $model->saldo_format,
            'iva' => $model->IVA,
            'total' => $model->Total,
            'serie' => $model->Serie,
            'folio' => $model->Folio,
            'folio_format' => $model->folio_formato,
            'moneda' => $model->moneda_descripcion,
            'idempresa' => $model->IdEmpresa,
            'total_format' => $model->total_format,
            'empresa_descripcion' => $model->empresa_descripcion,
            'empleado_descripcion' => $model->empleado_descripcion,
            'departamento' => $model->Departamento,
            'ubicacion' => $model->Ubicacion,
            'iddepartamento' => (int)$model->id_departamento,
            'id_proveedor' => $model->IdProveedor,
            'id_moneda' => $model->IdMoneda
        ];
    }
}
