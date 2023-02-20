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


    <div class="row">
        <div class="col-md-12" >
            {!! $cuerpo_correo !!}
        </div>
    </div>

</div>
</html>
