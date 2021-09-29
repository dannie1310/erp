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
    @if(!$reset)
    <div>Dirección: <a href="http://portal-aplicaciones.grupohi.mx/">http://portal-aplicaciones.grupohi.mx/</a></div>

    <p><b>Por favor revise el video que esta en el siguiente enlace para que aclare sus dudas sobre el inicio de sesión al portal de proveedores -> <a href="https://drive.google.com/file/d/1bI8LnyUVchzOfTRDF802Iiwhb7w_W66p/view?usp=sharing">Inicio de Sesión por Primera Vez</a></b></p>
    @endif
    <br>
    <small><b>Por favor NO RESPONDA A ESTE CORREO, se envió automáticamente.</b></small>
</div>
</html>
