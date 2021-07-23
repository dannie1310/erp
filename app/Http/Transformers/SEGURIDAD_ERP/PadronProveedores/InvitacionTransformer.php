<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;

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
        'sucursal'
    ];

    protected $availableIncludes = [
        'transaccion',
        'empresa',
        'sucursal',
        'solicitud_compra',
        'carta_terminos',
        'formato_cotizacion'
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
            'ubicacion_entrega_plataforma_digital' => $model->ubicacion_entrega_plataforma_digital,
            'direccion_entrega' => $model->direccion_entrega,
            'base_datos' => $model->base_datos,
            'nombre_usuario_invito' => $model->usuarioInvito->nombre_completo,
            'nombre_usuario_invitado' => ($model->usuarioInvitado->apaterno =="@")?$model->usuarioInvitado->nombre_completo_sin_espacios:$model->usuarioInvitado->nombre_completo,
            'fecha_hora_format' => $model->fecha_hora_format,
            'fecha_cierre_format' => $model->fecha_cierre_format,
            'cuerpo_correo' => ($model->cuerpo_correo)
        ];
    }

    public function includeTransaccion(Invitacion $model) {
        if ($item = $model->transaccionAntecedente) {
            return $this->item($item, new TransaccionTransformer);
        }
        return null;
    }

    public function includeSolicitudCompra(Invitacion $model) {
        if ($item = $model->solicitudAntecedente) {
            return $this->item($item, new SolicitudCompraTransformer);
        }
        return null;
    }

    public function includeEmpresa(Invitacion $model) {
        if ($item = $model->empresa) {
            return $this->item($item, new EmpresaTransformer);
        }
        return null;
    }

    public function includeSucursal(Invitacion $model) {
        if ($item = $model->sucursal) {
            return $this->item($item, new SucursalTransformer);
        }
        return null;
    }

    public function includeCartaTerminos(Invitacion $model) {
        if ($item = $model->cartaTerminos) {
            return $this->item($item, new InvitacionArchivoTransformer);
        }
        return null;
    }

    public function includeFormatoCotizacion(Invitacion $model) {
        if ($item = $model->formatoCotizacion) {
            return $this->item($item, new InvitacionArchivoTransformer);
        }
        return null;
    }

}
