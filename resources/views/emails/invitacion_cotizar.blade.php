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
    {!! $invitacion->cuerpo_correo !!}

    @if($invitacion->observaciones!='')
        <div class="row">
            <div class="col-md-12" >
                <label><b>Observaciones adicionales:</b></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                {{$invitacion->observaciones}}
            </div>
        </div>
            <br>
            <br>
    @endif

    @if($invitacion->usuarioInvitado->pide_cambio_contrasenia == 1)
        <div class="row">
            <div class="col-md-12" >
                <b><small>Por favor ingrese al portal con los datos de acceso que se le enviaron por correo previamente.</small></b>
            </div>
        </div>
    @endif
    @if($invitacion->archivos->count()>0)
        <div class="row">
            <div class="col-md-12" >
                <h4 style="color: #50b920">Consulte los archivos relacionados en el apartado 'Archivos' de la presente invitación.</h4>
            </div>
        </div>
    @endif
</div>
</html>
