<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;

use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use League\Fractal\TransformerAbstract;

class InvitacionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'transaccion',
    ];

    protected $availableIncludes = [
        'transaccion',
    ];

    public function transform(Invitacion $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'nombre_contacto' => $model->nombre_contacto,
            'numero_folio_format' => $model->numero_folio_format,
            'email' => $model->email,
            'obra' => $model->obra->nombre,
            'observaciones' => $model->observaciones,
            'base_datos' => $model->base_datos,
            'nombre_usuario_invito' => $model->usuarioInvito->nombre_completo,
            'nombre_usuario_invitado' => ($model->usuarioInvitado->apaterno =="@")?$model->usuarioInvitado->nombre_completo_sin_espacios:$model->usuarioInvitado->nombre_completo,
            'fecha_hora_format' => $model->fecha_hora_format,
            'fecha_cierre_format' => $model->fecha_cierre_invitacion_format
        ];
    }

    public function includeTransaccion(Invitacion $model) {
        if ($item = $model->transaccionAntecedente) {
            return $this->item($item, new TransaccionTransformer);
        }
        return null;
    }

}
