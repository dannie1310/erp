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
    <body>
        <p style="text-align: justify">
            Se le informa que <b>la invitación {{$invitacion->numero_folio_format}}</b> relacionada con la transacción
            <b>{{$invitacion->transaccionAntecedente->tipo_transaccion_str}} {{$invitacion->transaccionAntecedente->numero_folio_format}}</b>
            ha sido abierta el <b>{{$invitacion->fecha_apertura_format}}</b> a las <b>{{$invitacion->hora_apertura_format}} hrs.</b> por <b>{{$invitacion->razon_social}}</b>.
        </p>
    </body>
</html>
