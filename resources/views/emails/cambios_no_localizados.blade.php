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
    <div class="row">
        <div class="col-md-5" >
            <h3><i class="fa fa-arrow-circle-right"></i><b>Cambios en Proveedores No Localizados</b></h3>
            <table  class="table table-stripped" >
                <thead>
                <tr>
                    <th>RFC</th>
                    <th>Razón Social</th>
                </tr>
                </thead>
                <tbody>
                @if(count($altas)>0)
                    <tr style="background-color: #aaaaaa">
                        <td colspan="9">Nuevos</td>
                    </tr>
                    @foreach($altas as $alta)
                        <tr >
                            <td >{{$alta->rfc}}</td>
                            <td >{{$alta->razon_social}}</td>

                        </tr>

                    @endforeach
                @endif
                @if(count($bajas)>0)
                    <tr style="background-color: #aaaaaa">
                        <td colspan="9">Bajas</td>
                    </tr>
                    @foreach($bajas as $baja)
                    <tr >
                        <td >{{$baja->rfc}}</td>
                        <td >{{$baja->razon_social}}</td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
</html>
