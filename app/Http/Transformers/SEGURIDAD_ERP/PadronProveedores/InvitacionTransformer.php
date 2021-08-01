<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;

use App\Http\Transformers\CADECO\Compras\CotizacionCompraTransformer;
use App\Http\Transformers\CADECO\Compras\SolicitudCompraTransformer;
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\EmpresaTransformer;

class InvitacionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'transaccion',
        'solicitud_compra',
        'empresa',
        'sucursal',
        'cotizacion'

    ];

    protected $availableIncludes = [
        'transaccion',
        'empresa',
        'sucursal',
        'solicitud_compra',
        'carta_terminos',
        'formato_cotizacion',
        'cotizacion',
        'cotizacionCompra',
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
            'descripcion_obra' => $model->descripcion_obra,
            'observaciones' => $model->observaciones,
            'ubicacion_entrega_plataforma_digital' => $model->ubicacion_entrega_plataforma_digital,
            'direccion_entrega' => $model->direccion_entrega,
            'base_datos' => $model->base_datos,
            'id_obra' => $model->id_obra,
            'nombre_usuario_invito' => $model->nombre_usuario,
            'nombre_usuario_invitado' => ($model->usuarioInvitado->apaterno =="@")?$model->usuarioInvitado->nombre_completo_sin_espacios:$model->usuarioInvitado->nombre_completo,
            'fecha_hora_format' => $model->fecha_hora_format,
            'fecha_format' => $model->fecha_format,
            'fecha_cierre_format' => $model->fecha_cierre_invitacion_format,
            'tipo_antecedente' => $model->tipo_transaccion_antecedente,
            'importe_cotizacion' => $model->importe_cotizacion_format,
            'descripcion_sucursal' => $model->descripcion_sucursal,
            'direccion_sucursal' => $model->direccion_sucursal,
            'cuerpo_correo' => ($model->cuerpo_correo)
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
    public function includeSolicitudCompra(Invitacion $model) {
        if ($item = $model->solicitud) {
            return $this->item($item, new SolicitudCompraTransformer);
        }
        return null;
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Invitacion $model) {
        if ($item = $model->empresa) {
            return $this->item($item, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSucursal(Invitacion $model) {
        if ($item = $model->sucursal) {
            return $this->item($item, new SucursalTransformer);
        }
        return null;
    }

    /**
     * @param Invitacion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCartaTerminos(Invitacion $model) {
        if ($item = $model->cartaTerminos) {
            return $this->item($item, new InvitacionArchivoTransformer);
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
    public function includeFormatoCotizacion(Invitacion $model) {
        if ($item = $model->formatoCotizacion) {
            return $this->item($item, new InvitacionArchivoTransformer);
        }
        return null;
    }
}
