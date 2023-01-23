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
    <div id="header" align="center" style="width: 580px; height: 150px">
        <img src="http://seguimiento.grupohi.mx/assets/img/logo4.fw.png"><br><br>
        <h3>Factura {{ $factura->proyecto->proyecto }}</h3>
    </div>
    <br>
    <span style="font-weight:bold; color: #000000">Se le notifica que {{ auth()->user()->nombre }}  {{ auth()->user()->apaterno }}  {{auth()->user()->amaterno }} ha registrado una nueva factura</span>
    <br><br>
    <table cellpadding="0" cellspacing="0" style="width:600px;" id="cuerpo">
        <tr>
            <td class="campo" valign="top">FECHA</td><td class="valor" valign="top">{{ $factura->fecha }}</td>
        </tr>
            <tr>
                <td class="campo" valign="top">REFERENCIA</td><td class="valor" valign="top">{{ $factura->numero }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">PROYECTO</td><td class="valor" valign="top">{{ utf8_encode($factura->proyecto->proyecto) }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">AREA</td><td class="valor" valign="top">{{ $factura->proyecto->tipo->proyecto_tipo }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">EMPRESA</td><td class="valor" valign="top">{{ utf8_decode($factura->empresa->empresa) }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">CLIENTE</td><td class="valor" valign="top">{{ utf8_decode($factura->cliente->cliente) }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">PERIODO</td><td class="valor" valign="top">{{ $factura->fi_cubre }} al {{ $factura->ff_cubre }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">TOTAL DE FACTURA</td><td class="valor" align="right" valign="top">{{ $factura->total }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">MONEDA</td><td class="valor" valign="top">{{ $factura->moneda->moneda }}</td>
            </tr>
            <tr>
                <td class="campo" valign="top">DESCRIPCION</td><td class="valor" valign="top">{{ utf8_encode($factura->descripcion) }}</td>
            </tr>
            <tr>
                <td class="campo" style="text-align: center" colspan="2"><b>CONCEPTOS</b></td>
            </tr>
        @foreach($factura->conceptos as $concepto)
            <tr>
                <td class="campo" valign="top">{{ $concepto->tipoIngreso->tipo_ingreso }}</td>
                <td class="valor" align="right" valign="top"> {{number_format($concepto->importe, 2)}}</td>
            </tr>
        @endforeach
    </table>
    <br>
    <i><strong>
            <span class="leyenda">
                Mensaje enviado automaticamente desde el módulo de registro de ingresos SAO ERP
                <br>SAO - Hermes Infraestructura.
            </span>
        </strong>
    </i>
</div>
</html>
