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
    <h3></h3>
    <hr />
    <div class="row">
        <div class="col-md-5" >
            <label><b>Usuario Solicitó Busqueda:</b></label>

        </div>
        <div class="col-md-5" >
            {{$lote->usuario->nombre_completo}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-5" >
            <label><b>Fecha /  Hora Incio:</b></label>

        </div>
        <div class="col-md-5" >
            {{$lote->fecha_hora_inicio_format}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-5" >
            <label><b>Fecha y Hora Fin:</b></label>

        </div>
        <div class="col-md-5" >
            {{$lote->fecha_hora_fin_format}}
        </div>
    </div>

    @if ($lote->diferencias_detectadas)
        <div class="row">
            <div class="col-md-5" >
                <label><b>Diferencias Detectadas:</b></label>

            </div>
            <div class="col-md-5" >
                {{count($lote->diferencias_detectadas)}}
            </div>
        </div>
        @endif
    @if ($lote->diferencias_corregidas)
        <div class="row">
            <div class="col-md-5" >
                <label><b>Diferencias Corregidas:</b></label>
            </div>
            <div class="col-md-5" >
                {{count($lote->diferencias_corregidas)}}
            </div>
        </div>
    @endif

</div>
</html>