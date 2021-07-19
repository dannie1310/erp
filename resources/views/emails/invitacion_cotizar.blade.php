<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<div id="app">
    @if($invitacion->transaccionAntecedente->tipo_transaccion == 17)
    <b>Estimado(a) {{$invitacion->nombre_contacto}}</b>
    Se le invita cordialmente a cotizar los materiales que se describen en el archivo anexo, favor de cotizar el material que le compete.

    Razón Social a la que se tiene que facturar: {{$invitacion->obra->facturar}}
    Proyecto: {{$invitacion->obra->descripcion}}
    Localización del Proyecto:  {{$invitacion->obra->direccion}}
    Descripción:  {{$invitacion->transaccionAntecedente->observaciones}}
    Fecha de Cierre:  {{$invitacion->fecha_cierre_format}}


    Se requiere que realice el registro de su oferta en el portal a mas tardar el día {{$invitacion->fecha_cierre_format}}, con los siguientes puntos a considerar:

    1) El material debe ser entregado en el almacén del proyecto
    2) Considerar que se requieren certificados de calidad para su entrega en nuestros almacenes
    3) Considerar que este proyecto requiere crédito de 30 días a partir del ingreso de la factura a revisión, el pago está garantizado a través de un fideicomiso con el cliente
    4) Se requiere anexe fichas técnicas de material, donde indique que cumple con cada uno de los requerimientos.

    Favor de mandar confirmación al recibir este correo  a la dirección: {{$invitacion->usuarioInvito->correo}}.

    Sin mas por el momento quedamos al pendiente de su cotización.
    con folio {{$invitacion->transaccionAntecedente->numero_folio_format}} de la obra {{$invitacion->obra->nombre}} perteneciente a la empresa
    {{$invitacion->obra->facturar}}.
    <br>
    @if($invitacion->observaciones!='')
    <div class="row">
        <div class="col-md-12" >
            <label><b>Observaciones:</b></label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" >
            {{$invitacion->observaciones}}
        </div>
    </div>
    @endif

    @if($invitacion->usuarioInvitado->pide_cambio_contrasenia == 1)
        <div class="row">
            <div class="col-md-12" >
                Por favor ingrese al portal con los datos de acceso que se le enviaron previamente en otro correo.
            </div>
        </div>
    @endif
    @endif

</div>
</html>
