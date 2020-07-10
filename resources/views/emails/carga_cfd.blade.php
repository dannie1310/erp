<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>

</head>
<div id="app">
    <div class="row">
    <div class="col-md-5" >
        <h3><i class="fa fa-arrow-circle-right"></i><b>Resultado del procesamiento</b></h3>

    <table  class="table table-stripped">
        <tbody>
        <tr >
            <th style="text-align: left" >No. de Carga:</th>
            <td style="text-align: right">{{$carga->id}}</td>
        </tr>
        <tr >
            <th style="text-align: left" >Usuario:</th>
            <td style="text-align: right">{{$carga->usuario->nombre_completo}}</td>
        </tr>
        <tr >
            <th style="text-align: left" >Duración de procesamiento (segundos):</th>
            <td style="text-align: right">{{$carga->duracion}}</td>
        </tr>
        <tr >
            <th style="text-align: left" >Núm. archivos leidos:</th>
            <td style="text-align: right">{{$carga->archivos_leidos}}</td>
        </tr>
        <tr >
            <th style="text-align: left" >Núm. archivos cargados:</th>
            <td style="text-align: right">{{$carga->archivos_cargados}}</td>
        </tr>
        @if($carga->proveedores_nuevos>0)
        <tr >
            <th style="text-align: left" >Núm. proveedores cargados:</th>
            <td style="text-align: right">{{$carga->proveedores_nuevos}}</td>
        </tr>
        @endif
        @if($carga->archivos_no_cargados>0)
        <tr >
            <th style="text-align: left" >Núm. archivos no cargados:</th>
            <td style="text-align: right">{{$carga->archivos_no_cargados}}</td>
        </tr>
        @endif
        @if($carga->archivos_preexistentes>0)
        <tr >
            <td style="text-align: left" >-Núm. archivos preexistentes:</td>
            <td style="text-align: right">{{$carga->archivos_preexistentes}}</td>
        </tr>
        @endif
        @if($carga->archivos_receptor_no_valido>0)
        <tr >
            <td style="text-align: left" >-Núm. archivos receptor no válido:</td>
            <td style="text-align: right">{{$carga->archivos_receptor_no_valido}}</td>
        </tr>
        @endif
        @if($carga->archivos_no_cargados_error_app>0)
        <tr >
            <td style="text-align: left" >-Núm. archivos error app:</td>
            <td style="text-align: right">{{$carga->archivos_no_cargados_error_app}}</td>
        </tr>
        @endif
        @if($carga->archivos_corruptos>0)
        <tr >
            <td style="text-align: left" >-Núm. archivos corruptos:</td>
            <td style="text-align: right">{{$carga->archivos_corruptos}}</td>
        </tr>
        @endif
        @if($carga->archivos_tipo_incorrecto>0)
        <tr >
            <td style="text-align: left" >-Núm. archivos tipo incorrecto:</td>
            <td style="text-align: right">{{$carga->archivos_tipo_incorrecto}}</td>
        </tr>
        @endif
        </tbody>
    </table>
    </div>
    </div>
</div>
</html>