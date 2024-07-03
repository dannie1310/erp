<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        #cuerpo{
            margin:0;
            padding:10px;
            font-family:Arial, Helvetica, sans-serif;
            font-size:12px;
            border:solid 1px #ccc;
            width:600px;
            max-width:600px;
            color: #000000;
        }
        #header{
            background-color:#efefef;
            padding:10px;
            color: #000000;
        }
        .campo{
            background-color:#888888;
            color:#000000;
            font-weight:300;
            padding:5px;
            font-size:12px;
            text-align:right;
        }
        .valor{
            background-color:#efefef;
            padding:5px;
            font-size:12px;
            color: #000000;
        }
        .leyenda{
            font-size:10px;
            color: #000000;
        }
    </style>
</head>
<div id="app">
    <span style="font-weight:bold; color: #000000">Se le notifica el envió del XML para migración a IFS</span>
    <br>
    <i><strong>
            <span class="leyenda">
                Mensaje enviado desde el módulo de documentos de recursos SAO ERP
                <br>SAO - Hermes Infraestructura.
            </span>
        </strong>
    </i>
</div>
</html>
