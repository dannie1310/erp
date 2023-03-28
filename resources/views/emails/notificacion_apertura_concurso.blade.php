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
            Se le informa que la apertura del concurso {{$concurso->nombre}} ha finalizado.
            <br><br>
            Primer Lugar: <strong>{{$concurso->participanteGanador->nombre}} {{$concurso->participanteGanador->monto_format}}</strong>
            <br>
            Oferta Hermes: <strong>{{$concurso->participanteHermes->nombre}} {{$concurso->participanteHermes->monto_format}}</strong>
            <br>
            Resultado: <strong>{{$concurso->resultado_txt}}</strong>
            <br>
            Promedio de Ofertas: <strong>{{$concurso->promedio_format}}</strong>
            <br>
            Diferencia Oferta Hermes vs Primer Lugar: <strong>{{$concurso->participanteHermes->distancia_primer_lugar_format}} ({{$concurso->participanteHermes->distancia_primer_lugar_porcentaje}})</strong>

            <br>
            <br>
            <img src="https://{{$_SERVER['SERVER_NAME']}}:{{$_SERVER['SERVER_PORT']}}/api/concursos/concurso-scope/{{$concurso->id}}/grafica-png?access_token={{$token}}" class="img-responsive img-rounded" width="100%">
        </div>
    </div>

</div>
</html>
