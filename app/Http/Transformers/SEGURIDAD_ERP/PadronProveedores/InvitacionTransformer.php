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

    public function transform(Invitacion $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'nombre_contacto' => $model->no_imss,
            'email' => $model->estado_expediente->descripcion,
            'obra' => $model->avance_expediente,
            'nombre_usuario_invito' => $model->usuarioInvito->nombre_completo,
            'fecha_hora_invitacion_format' => $model->fecha_hora_invitacion_format
        ];
    }

    public function includeTransaccion(Invitacion $model) {
        if ($item = $model->transaccionAntecedente) {
            return $this->item($item, new TransaccionTransformer);
        }
        return null;
    }

}
