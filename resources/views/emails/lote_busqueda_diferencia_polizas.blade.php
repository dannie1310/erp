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
        <div class="col-md-3" >
            <div class="form-group" >
                <label><b>Usuario Solicitó Busqueda:</b></label>
                {{$lote->usuario->nombre_completo}}
            </div>

        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>Fecha /  Hora Incio:</b></label>
                {{$lote->fecha_hora_inicio_format}}
            </div>
        </div>
        <div class="col-md-2" >
            <div class="form-group" >
                <label><b>Fecha /  Hora Fin:</b></label>
                {{$lote->fecha_hora_fin_format}}
            </div>
        </div>
        @if ($lote->diferencias_detectadas)
            <div class="col-md-2" >
                <div class="form-group" >
                    <label><b>Diferencias Detectadas:</b></label>
                    {{count($lote->diferencias_detectadas)}}
                </div>
            </div>
        @endif
        @if ($lote->diferencias_corregidas)
            <div class="col-md-2" >
                <div class="form-group" >
                    <label><b>Diferencias Corregidas:</b></label>
                    {{count($lote->diferencias_corregidas)}}
                </div>
            </div>
        @endif
        <hr />
        @if ($diferencias_totales)
            <div class="col-md-2" >
                <div class="form-group" >
                    <label><b>Diferencias Totales Existentes:</b></label>
                    {{$diferencias_totales->sum("cantidad")}}
                </div>
            </div>
        @endif
    </div>

    @if(count($lote->cantidad_diferencias_detectadas_por_tipo)>0)
        <hr />

        <div class="row">
            <table>
                <caption style="text-align: left; font-weight: bold">Cantidad de diferencias por tipo:</caption>
                <thead>
                <th>
                    Tipo Diferencia
                </th>
                <th>
                    Cantidad
                </th>
                </thead>
                <tbody>
                @foreach($lote->cantidad_diferencias_detectadas_por_tipo as $item)
                <tr>
                    <td>
                        {{$item->descripcion}}
                    </td>
                    <td style="text-align: right">
                        {{$item->cantidad}}
                    </td>
                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    @if(count($lote->cantidad_diferencias_detectadas_por_tipo_por_base)>0)
        <hr />

        <div class="row">
            <table>
                <caption style="text-align: left; font-weight: bold">Cantidad de diferencias por empresa y tipo:</caption>
                <thead>
                <th>
                    Empresa Revisada
                </th>
                <th>
                    Empresa Referencia
                </th>
                <th>
                    Tipo Diferencia
                </th>
                <th>
                    Cantidad
                </th>
                </thead>
                <tbody>
                @foreach($lote->cantidad_diferencias_detectadas_por_tipo_por_base as $item)
                    <tr>
                        <td>
                            {{$item->base_datos_revisada}}
                        </td>
                        <td>
                            {{$item->base_datos_referencia}}
                        </td>
                        <td>
                            {{$item->descripcion}}
                        </td>
                        <td style="text-align: right">
                            {{$item->cantidad}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(count($diferencias_totales)>0)
        <hr />

        <div class="row">
            <table>
                <caption style="text-align: left; font-weight: bold">Cantidad de diferencias totales por empresa y tipo:</caption>
                <thead>
                <th>
                    Empresa Revisada
                </th>
                <th>
                    Empresa Referencia
                </th>
                <th>
                    Tipo Diferencia
                </th>
                <th>
                    Cantidad
                </th>
                </thead>
                <tbody>
                @foreach($diferencias_totales as $item)
                    <tr>
                        <td>
                            {{$item->base_datos_revisada}}
                        </td>
                        <td>
                            {{$item->base_datos_referencia}}
                        </td>
                        <td>
                            {{$item->descripcion}}
                        </td>
                        <td style="text-align: right">
                            {{$item->cantidad}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</html>