<?php


namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\FinanzasCBE\SolicitudMovimiento;
use League\Fractal\TransformerAbstract;

class SolicitudMovimientoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'usuario'
    ];

    public function transform(SolicitudMovimiento $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha' => $model->fecha_hora,
            'id_solicitud' => $model->id_solicitud,
            'fecha_format' => $model->fecha_format,
            'observaciones' => $model->observaciones,
            'id_tipo_movimiento' => $model->id_tipo_movimiento,
            'mac_address' => $model->mac_address,
            'ip' => $model->ip,
            'movimiento' => $model->tipo_movimiento
        ];
    }

    public function includeUsuario(SolicitudMovimiento $model)
    {
        if($usuario = $model->registro)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

}