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
    <p style="text-align: justify">
        Se le informa que el proveedor {{$cotizacion->empresa->razon_social}} ha enviado la cotización {{$cotizacion->numero_folio_format}} en respuesta a la invitación {{$invitacion->numero_folio_format}} del proyecto {{$invitacion->descripcion_obra}}.
    </p>
    <small><b>Por favor no responda a este correo, se envió automáticamente.</b></small>
</div>
</html>
