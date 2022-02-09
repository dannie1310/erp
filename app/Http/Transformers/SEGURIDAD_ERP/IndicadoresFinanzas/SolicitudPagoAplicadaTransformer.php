<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\IndicadoresFinanzas;


use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\CFDSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\EmpresaSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Contabilidad\ProveedorSATTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\ObraTransformer;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use League\Fractal\TransformerAbstract;

class SolicitudPagoAplicadaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    /**
     * @param SolicitudPagoAplicada $model
     * @return array
     */
    public function transform(SolicitudPagoAplicada $model)
    {
        $solicitud = $model->toArray();
        //dd($transaccion);
        //$solicitud =["estado"=>$model->estado,"id_transaccion"=>$model->id_transaccion];
        $complemento = [
            'fecha_solicitud_format'=>$model->fecha_solicitud_format,
            'numero_folio_format'=>$model->numero_folio_format,
            'monto_format'=>$model->monto_format,
            'remesa_relacionada'=>$model->remesa_relacionada,
            'monto_autorizado'=>$model->monto_autorizado_remesa,
            'monto_autorizado_format'=>$model->monto_autorizado_format,
            'monto_aplicado_format'=>$model->monto_aplicado_format,
            'monto_pagado_format'=>$model->monto_pagado_format,
            'pendiente_format'=>$model->pendiente_format,
            'observaciones'=>$model->observaciones,
            'usuario_valido'=>$model->usuario_valido

        ];
        return array_merge($solicitud, $complemento);
    }

}
