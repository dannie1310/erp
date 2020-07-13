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
            <h3><i class="fa fa-arrow-circle-right"></i><b>Cambios en EFOS</b></h3>
            <table  class="table table-stripped">
                <thead>
                <tr>
                    <th>EFO</th>
                    <th>RFC</th>
                    <th>Estado Inicial</th>
                    <th>Estado Final</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cambios as $cambio)
                <tr >
                    <td >{{$cambio->efos->razon_social}}</td>
                    <td >{{$cambio->efos->rfc}}</td>
                    <td >{{$cambio->estado_inicial}}</td>
                    <td >{{$cambio->estado_final}}</td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
</html>
