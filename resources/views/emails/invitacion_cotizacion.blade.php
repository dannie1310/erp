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
    <h3>Invitación a cotizar</h3>
    <hr />
    Estimado {{$invitacion->contacto}}, se le informa que ha sido invitado a cotizar XX XXXXXX con folio XXXXXXXX de la obra {{$invitacion->obra->nombre}} perteneciente a la empresa
    {{$invitacion->obra->facturar}}.
    <br>
    @if($invitacion->observaciones!='')
    <div class="row">
        <div class="col-md-12" >
            <label><b>Observaciones:</b></label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" >
            {{$invitacion->observaciones}}
        </div>
    </div>
    @endif
    @if($credenciales)
        <div class="row">
            <div class="col-md-12" >
                Por favor ingrese a este enlace para realizar el registro de su cotización: link con los siguientes datos:
                Usuario:{{$credenciales->usuario}}
                Clave: {{$credenciales->password}}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12" >
                Por favor ingrese a este enlace para realizar el registro de su cotización: link
            </div>
        </div>
    @endif
</div>
</html>
