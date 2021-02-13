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
    <div class="col-md-2" >
        <div class="form-group" >
            <label><b>Folio:</b></label>
            {{$solicitud->id}}
        </div>
    </div>

    <div class="row">
        <div class="col-md-3" >
            <div class="form-group" >
                <label><b>Usuario Solicitó Busqueda:</b></label>
                {{$solicitud->usuario->nombre_completo}}
            </div>

        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>Fecha /  Hora Incio:</b></label>
                {{$solicitud->fecha_hora_inicio_format}}
            </div>
        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>Fecha /  Hora Fin:</b></label>
                {{$solicitud->fecha_hora_fin_format}}
            </div>
        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>No. Empresas con Acceso:</b></label>
                {{count($solicitud->partidas()->where("sin_acceso","=",0)->get())}}
            </div>
        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>No. Empresas sin Acceso:</b></label>
                {{count($solicitud->partidas()->where("sin_acceso","=",1)->get())}}
            </div>
        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>No. Asociaciones Detectadas:</b></label>
                {{$solicitud->cantidad_asociaciones_detectadas_format}}
            </div>
        </div>

        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>No. Nuevas Asociaciones:</b></label>
                {{$solicitud->cantidad_asociaciones_nuevas_format}}
            </div>
        </div>

        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>No. Asociaciones Canceladas:</b></label>
                {{$solicitud->cantidad_asociaciones_eliminadas_format}}
            </div>
        </div>

        @if(count($solicitud->partidas()->where("sin_acceso","=",0)->get())>0)
            <hr />

            <div class="row">
                <table>
                    <caption style="text-align: left; font-weight: bold">Empresas Con Acceso:</caption>
                    <thead>
                    <th>
                        BD
                    </th>
                    <th>
                        Empresa
                    </th>
                    <th>
                        Asociaciones Identificadas
                    </th>
                    <th>
                        Asociaciones Nuevas
                    </th>
                    <th>
                        Asociaciones Eliminadas
                    </th>
                    </thead>
                    <tbody>
                    @foreach($solicitud->partidas()->where("sin_acceso","=",1)->get() as $item)
                        <tr>
                            <td>
                                {{$item->base_datos}}
                            </td>
                            <td>
                                {{$item->nombre_empresa}}
                            </td>
                            <td style="text-align: right">
                                {{$item->cantidad_asociaciones_detectadas}}
                            </td>
                            <td style="text-align: right">
                                {{$item->cantidad_asociaciones_nuevas}}
                            </td>
                            <td style="text-align: right">
                                {{$item->cantidad_asociaciones_eliminadas}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @if(count($solicitud->partidas()->where("sin_acceso","=",1)->get())>0)
        <hr />

        <div class="row">
            <table>
                <caption style="text-align: left; font-weight: bold">Empresas Sin Acceso:</caption>
                <thead>
                <th>
                    BD
                </th>
                <th>
                    Empresa
                </th>
                </thead>
                <tbody>
                @foreach($solicitud->partidas()->where("sin_acceso","=",1)->get() as $item)
                    <tr>
                        <td>
                            {{$item->base_datos}}
                        </td>
                        <td>
                            {{$item->nombre_empresa}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</html>
