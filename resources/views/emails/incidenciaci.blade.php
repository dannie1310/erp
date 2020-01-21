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
    <h3>{{$incidencia->tipo->descripcion}}</h3>
    <hr />
    <div class="row">
        <div class="col-md-5" >
            <label><b>Usuario:</b></label>

        </div>
        <div class="col-md-5" >
            {{$incidencia->usuario->nombre_completo}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-5" >
            <label><b>Fecha y Hora de Incidencia:</b></label>

        </div>
        <div class="col-md-5" >
            {{$incidencia->fecha_hora_registro_format}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-5" >
            <label><b>Proyecto:</b></label>

        </div>
        <div class="col-md-5" >
            {{$incidencia->obra}} ({{$incidencia->base_datos}})
        </div>
    </div>
    <hr />

    @if ($incidencia->rfc)
        <div class="row">
            <div class="col-md-5" >
                <label><b>RFC:</b></label>

            </div>
            <div class="col-md-5" >
                {{$incidencia->rfc}}
            </div>
        </div>
    @endif
    @if ($incidencia->empresa)
        <div class="row">
            <div class="col-md-5" >
                <label><b>Empresa:</b></label>

            </div>
            <div class="col-md-5" >
                {{$incidencia->empresa}}
            </div>
        </div>
        @endif
    @if ($incidencia->tipo_transaccion)
        <div class="row">
            <div class="col-md-5" >
                <label><b>Transacción:</b></label>
            </div>
            <div class="col-md-5" >
                {{$incidencia->tipo_transaccion}} {{$incidencia->folio_transaccion}}
            </div>
        </div>
    @endif
    @if ($incidencia->mensaje)
        <div class="row">
            <div class="col-md-5" >
                <label><b>Mensaje:</b></label>
            </div>
            <div class="col-md-5" >
                {{$incidencia->mensaje}}
            </div>
        </div>
    @endif

</div>
</html>