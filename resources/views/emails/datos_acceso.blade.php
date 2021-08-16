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
    <div>Usuario: {{$usuario}}</div>
    <div>Contraseña: {{$clave}}</div>
    <div>Dirección: <a href="http://portal-aplicaciones.grupohi.mx/">http://portal-aplicaciones.grupohi.mx/</a></div>
    <br>
    <small><b>Por favor no responda a este correo, se envió automáticamente.</b></small>
</div>
</html>
