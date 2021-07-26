<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;

use App\Http\Transformers\CADECO\Compras\CotizacionCompraTransformer;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use League\Fractal\TransformerAbstract;

class InvitacionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
        'transaccion',
        'cotizacion',
        'cotizacionCompra',
        'solicitudCompra'
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
            'obra' => $model->nombre_obra,
            'observaciones' => $model->observaciones,
            'base_datos' => $model->base_datos,
            'nombre_usuario_invito' => $model->nombre_usuario,
            'nombre_usuario_invitado' => ($model->usuarioInvitado->apaterno =="@")?$model->usuarioInvitado->nombre_completo_sin_espacios:$model->usuarioInvitado->nombre_completo,
            'fecha_hora_format' => $model->fecha_hora_format,
            'fecha_cierre_format' => $model->fecha_cierre_invitacion_format,
            'tipo_antecedente' => $model->tipo_transaccion_antecedente,
            'importe_cotizacion' => $model->importe_cotizacion_format,
            'descripcion_sucursal' => $model->descripcion_sucursal,
            'direccion_sucursal' => $model->direccion_sucursal,
        ];
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTransaccion(Invitacion $model)
    {
        if ($transaccion = $model->transaccionAntecedente)
        {
            return $this->item($transaccion, new TransaccionTransformer);
        }
        return null;
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacion(Invitacion $model)
    {
        if($cotizacion = $model->cotizacionGenerada)
        {
            return $this->item($cotizacion, new TransaccionTransformer);
        }
        return null;
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCotizacionCompra(Invitacion $model)
    {
        if($cotizacion_compra = $model->cotizacionCompra)
        {
            return $this->item($cotizacion_compra, new CotizacionCompraTransformer);
        }
        return null;
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSolicitudCompra(Invitacion $model)
    {
        if($solicitud = $model->solicitud)
        {
            return $this->item($solicitud, new SolicitudCompraTransformer);
        }
        return null;
    }
}
