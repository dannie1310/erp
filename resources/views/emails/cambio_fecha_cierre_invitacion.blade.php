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
            Se le informa que <b>la nueva fecha de cierre de la invitación {{$invitacion->numero_folio_format}}</b> es: {{$invitacion->fecha_cierre_invitacion_format}}.
        </p>
    </body>
</html>
