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
            'email' => $model->email,
            'obra' => $model->nombre_obra,
            'nombre_usuario_invito' => $model->nombre_usuario,
        ];
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTransaccion(Invitacion $model) {
        if ($transaccion = $model->transaccionAntecedente) {
            return $this->item($transaccion, new TransaccionTransformer);
        }
        return null;
    }
}
