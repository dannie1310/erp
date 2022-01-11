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
    @if($invitacion->razon_social!='')
        @if(is_numeric(substr($invitacion->rfc,3,1)))
            Se le informa que la empresa {{$invitacion->razon_social}} ha sido invitada a cotizar en el proyecto {{$invitacion->obra->nombre}} de la empresa {{$invitacion->obra->facturar}} y usted ha sido incluido para recibir las notificaciones relacionadas con el proceso.
        @else
            Se le informa que {{$invitacion->razon_social}} ha sido invitado a cotizar en el proyecto {{$invitacion->obra->nombre}} de la empresa {{$invitacion->obra->facturar}} y usted ha sido incluido para recibir las notificaciones relacionadas con el proceso.
        @endif
    @else
        Se le informa que {{$invitacion->nombre_contacto}} ha sido invitado a cotizar en el proyecto {{$invitacion->obra->nombre}} de la empresa {{$invitacion->obra->facturar}} y usted ha sido incluido para recibir las notificaciones relacionadas con el proceso.
    @endif

</div>
</html>
