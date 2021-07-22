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

        @if($invitacion->ubicacion_entrega_plataforma_digital != "")
        <li>
            Localización del Proyecto (Plataforma Digital):  <a href="{{$invitacion->ubicacion_entrega_plataforma_digital}}">{{$invitacion->ubicacion_entrega_plataforma_digital}}</a>
        </li>
        @endif

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
        <li>
            Se requiere anexe firmada la carta de términos y condiciones adjunta a esta invitación.
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
    <img style='display:block;margin-left: auto; margin-right: auto;' scr="http://127.0.0.1:8000/api/compras/invitacion-cotizar/abierto/{{$invitacion->id}}?access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjIxNmY4MGU0MTI5ODI0Y2E4NzczMmVjNzY1OWI3YjA5OGE2Y2I4NmQwMTdmZWJiY2ZlNWQ2NGE5MGI5NWE1YzBlYTNjOGQ4MDU5MTE3NTQyIn0.eyJhdWQiOiIxIiwianRpIjoiMjE2ZjgwZTQxMjk4MjRjYTg3NzMyZWM3NjU5YjdiMDk4YTZjYjg2ZDAxN2ZlYmJjZmU1ZDY0YTkwYjk1YTVjMGVhM2M4ZDgwNTkxMTc1NDIiLCJpYXQiOjE2MjY4MTExMjYsIm5iZiI6MTYyNjgxMTEyNiwiZXhwIjoxNjU4MzQ3MTI1LCJzdWIiOiIxODAiLCJzY29wZXMiOltdfQ.dyrhyEkoMHJ0oB_Y1sJrHriziZP-LwO4V9RqBeDGGoS0yZaSWsEkHNChbwx0M7hOp7uizlw23uF9odVokNVmjLhNTPEyHpK9hFtC03uc9c20GxBhnq5SbM4GAYLnrN5yotyqjP3YSh4cea8QWSNELcOmyrt_Aefv_G_tJqdGIlj79MXd8wfVR5JYbcQ3LHxfkXa6ppHeGAYUQYGMN8qfaipUamqlnFi4VpyEABD0hC_3QWKsCt4r5S4V-njdq9mINTYMz3ogHfPArTY0-sbSk-mL7Xuk-_wPcRGDmYWbD50izgBlVV98BIrU95Gw_Ztng-arhBFTBeoATj3JizEWViv8vSnKI0bF9ggcs423Jlkbm7iReS7rTTSJHs0M6mrjODZSQ0nBiV-WziZuDlu6xnqd8tnZwTFQq7CLaQ0rYdnwlTNECHU4wnOkbgamaNkKiWtQGSJxHK2rfaGkth3Acq_LOE0_rczls4RCgZA0vV1iQMopAGM1YhH8N5WgJ-4sqZjuuS3UOHLopYW0Q_7klaeVeg_mfjP1zp8Y6LEEWoAuZoFNqreUMxX11sBzKlrY7-citDBizcUJpmJ99leA4Mw7qwI5WqK4oivbAg9q68MVNH6NEK9AdlPHJggpN20vawNoLaQbM-fvj0JhrMu8QwqkuSCmzdc89gTI4k8b68E"/>
</div>
</html>
