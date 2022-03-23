<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:44 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\Compras\OrdenCompraTransformer;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Http\Transformers\CADECO\CostoTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use League\Fractal\TransformerAbstract;

class SolicitudPagoTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario',
        'empresa',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Solicitud $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio_format,
            'antecedente' => $model->id_antecedente,
            'subtotal'=>(float)$model->subtotal,
            'subtotal_format'=>(string) '$ '.number_format(($model->subtotal),2,".",","),
            'impuesto'=>(float)$model->impuesto,
            'impuesto_format'=>(string) '$ '.number_format($model->impuesto,2,".",","),
            'monto'=>(float)$model->monto,
            'total_format'=>(string)$model->monto_format,
            'monto_format'=>(string)$model->monto_format,
            'referencia'=>(string)$model->referencia,
            'observaciones'=>(string)$model->observaciones,
            'tipo_solicitud'=>(int) $model->tipo_transaccion,
            'fecha_format' => (string)$model->fecha_hora_registro_format,
            'fecha_registro_format' => (string)$model->fecha_hora_registro_format,
            'fecha_solicitud_format' => (string)$model->fecha_format,
            'estado' => (int)$model->estado,
            'cumplimiento' => (string)$model->cumplimiento_form,
            'vencimiento' => $model->vencimiento_form,
        ];
    }


}
