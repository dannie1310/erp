<?php

namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;

use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion;

use League\Fractal\TransformerAbstract;

class SolicitudPagoAutorizacionTransformer extends TransformerAbstract
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

    public function transform(SolicitudPagoAutorizacion $model)
    {
        return [
            'id' => $model->getKey(),
            'usuario_registro' => $model->usuario_registro,
            'usuario_autorizo' => $model->usuario_autorizo,
            'usuario_rechazo' => $model->usuario_rechazo,
            "base_datos"=>$model->base_datos,
            'proyecto'=>$model->proyecto,
            'numero_folio'=>$model->numero_folio_format,
            'fecha'=>$model->fecha_format,
            'fecha_hora_registro'=>$model->fecha_hora_registro_format,
            'fecha_registro'=>$model->fecha_registro,
            'fecha_autorizacion'=>$model->fecha_hora_autorizacion,
            'fecha_rechazo'=>$model->fecha_hora_rechazo,
            'razon_social'=>$model->razon_social,
            'rfc'=>$model->rfc,
            'observaciones'=>$model->observaciones,
            'monto'=>$model->monto_format,
            'moneda'=>$model->moneda,
            'estado'=>$model->estado,
            'tipo_txt'=>$model->tipo_txt,
            'registro'=>$model->registro
        ];
    }

}
