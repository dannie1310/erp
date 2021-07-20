<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        li {
            margin: 0;
        }
    </style>

</head>
<div id="app">
    @if($invitacion->transaccionAntecedente->tipo_transaccion == 17)
    <b>Estimado(a) {{$invitacion->nombre_contacto}}</b>
    <br>
    <br>
    Se le invita cordialmente a cotizar los materiales que se describen en el archivo anexo, favor de cotizar el material que le compete.
    <br>

    <ul style="list-style-type: none; padding: 0px">
        <li>
            Razón Social a la que se tiene que facturar: {{$invitacion->obra->facturar}}
        </li>
        <li>
            RFC: {{$invitacion->obra->rfc}}
        </li>
        <li>
            Proyecto: {{$invitacion->obra->descripcion}}
        </li>
        <li>
            Localización del Proyecto:  {{$invitacion->direccion_entrega}}
        </li>
        <li>
            Descripción:  {{$invitacion->transaccionAntecedente->observaciones}}
        </li>
        <li>
            Fecha de Cierre:  {{$invitacion->fecha_cierre_format}}
        </li>
    </ul>
    <br>

    Se requiere que realice el registro de su cotización en el <a href="http://portal-aplicaciones.grupohi.mx/">portal</a> a mas tardar el día {{$invitacion->fecha_cierre_format}}, con los siguientes puntos a considerar:
    <br>
    <ol>
        <li>
            El material debe ser entregado en el almacén del proyecto
        </li>
        <li>
            Considerar que se requieren certificados de calidad para su entrega en nuestros almacenes
        </li>
        <li>
            Considerar que este proyecto requiere crédito de 30 días a partir del ingreso de la factura a revisión, el pago está garantizado a través de un fideicomiso con el cliente
        </li>
        <li>
            Se requiere anexe fichas técnicas de material, donde indique que cumple con cada uno de los requerimientos.
        </li>
    </ol>

    <br>

    @if($invitacion->observaciones!='')
        <div class="row">
            <div class="col-md-12" >
                <label><b>Observaciones adicionales:</b></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                {{$invitacion->observaciones}}
            </div>
        </div>
            <br>
            <br>
    @endif
    Favor de mandar confirmación al recibir este correo  a la dirección: {{$invitacion->usuarioInvito->correo}}, no responder al correo actual dado que se envió de manera automática.
    <br>
    <br>
    Sin mas por el momento quedamos al pendiente del registro de su cotización.
    <br>
    <br>
    @if($invitacion->usuarioInvitado->pide_cambio_contrasenia == 1)
        <div class="row">
            <div class="col-md-12" >
                <b><small>Por favor ingrese al portal con los datos de acceso que se le enviaron por correo previamente.</small></b>
            </div>
        </div>
    @endif
    @endif

</div>
</html>
