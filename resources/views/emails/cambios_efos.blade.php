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
            <table  class="table table-stripped" >
                <thead>
                <tr>
                    <th>RFC EFO</th>
                    <th>EFO</th>
                    <th>Estado Inicial</th>
                    <th>Estado Final</th>
                    <th>#F</th>
                    <th>#NC</th>
                    <th>Monto F</th>
                    <th>Monto NC</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                @if(count($cambios->where("estado_final",0))>0)
                    <tr style="background-color: #aaaaaa">
                        <td colspan="9">Nuevos Definitivos</td>
                    </tr>
                    @foreach($cambios->where("estado_final",0) as $cambio)
                        <tr style="background-color: #cccccc">
                            <td >{{$cambio->efos->rfc}}</td>
                            <td >{{$cambio->efos->razon_social}}</td>
                            <td >{{$cambio->estado_inicial_txt}}</td>
                            <td >{{$cambio->estado_final_txt}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->count(),0,"",",")}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->count(),0,"",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total")-$cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                        </tr>
                        @foreach($cambio->empresas as $empresa)
                            <tr >
                                <td >{{$empresa->rfc}}</td>
                                <td colspan="3">{{$empresa->razon_social}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total") - $empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                @if(count($cambios->where("estado_final",2))>0)
                    <tr style="background-color: #aaaaaa">
                        <td colspan="9">Nuevos Presuntos</td>
                    </tr>
                    @foreach($cambios->where("estado_final",2) as $cambio)
                    <tr style="background-color: #cccccc">
                        <td >{{$cambio->efos->rfc}}</td>
                        <td >{{$cambio->efos->razon_social}}</td>
                        <td >{{$cambio->estado_inicial_txt}}</td>
                        <td >{{$cambio->estado_final_txt}}</td>
                        <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->count(),0,"",",")}}</td>
                        <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->count(),0,"",",")}}</td>
                        <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total"),2,".",",")}}</td>
                        <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                        <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total")-$cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                    </tr>
                    @foreach($cambio->empresas as $empresa)
                        <tr >
                            <td >{{$empresa->rfc}}</td>
                            <td colspan="3">{{$empresa->razon_social}}</td>
                            <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                            <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                            <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total") - $empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                        </tr>
                    @endforeach
                @endforeach
                @endif
                @if(count($cambios->where("estado_final",1))>0)
                    <tr style="background-color: #aaaaaa">
                        <td colspan="9">Nuevos Desvirtuados</td>
                    </tr>
                    @foreach($cambios->where("estado_final",1) as $cambio)
                        <tr style="background-color: #cccccc" >
                            <td >{{$cambio->efos->rfc}}</td>
                            <td >{{$cambio->efos->razon_social}}</td>
                            <td >{{$cambio->estado_inicial_txt}}</td>
                            <td >{{$cambio->estado_final_txt}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->count(),0,"",",")}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->count(),0,"",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total")-$cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                        </tr>
                        @foreach($cambio->empresas as $empresa)
                            <tr >
                                <td >{{$empresa->rfc}}</td>
                                <td colspan="3">{{$empresa->razon_social}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total") - $empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                @if(count($cambios->where("estado_final",3))>0)
                    <tr style="background-color: #aaaaaa">
                        <td colspan="9">Nuevos Sentencia Favorable</td>
                    </tr>
                    @foreach($cambios->where("estado_final",3) as $cambio)
                        <tr style="background-color: #cccccc">
                            <td >{{$cambio->efos->rfc}}</td>
                            <td >{{$cambio->efos->razon_social}}</td>
                            <td >{{$cambio->estado_inicial_txt}}</td>
                            <td >{{$cambio->estado_final_txt}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->count(),0,"",",")}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->count(),0,"",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total")-$cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                        </tr>
                        @foreach($cambio->empresas as $empresa)
                            <tr >
                                <td >{{$empresa->rfc}}</td>
                                <td colspan="3">{{$empresa->razon_social}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total") - $empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                @if(count($cambios->where("estado_final",4))>0)
                <tr style="background-color: #aaaaaa">
                    <td colspan=9">Nuevos Situación del Contribuyente</td>
                </tr>
                    @foreach($cambios->where("estado_final",4) as $cambio)
                        <tr style="background-color: #cccccc">
                            <td >{{$cambio->efos->rfc}}</td>
                            <td >{{$cambio->efos->razon_social}}</td>
                            <td >{{$cambio->estado_inicial_txt}}</td>
                            <td >{{$cambio->estado_final_txt}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->count(),0,"",",")}}</td>
                            <td style="text-align: right">{{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->count(),0,"",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right">${{number_format($cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                            <td style="text-align: right" >${{number_format($cambio->efos->cfd->where("tipo_comprobante","I")->sum("total")-$cambio->efos->cfd->where("tipo_comprobante","E")->sum("total"),2,".",",")}}</td>
                        </tr>
                        @foreach($cambio->empresas as $empresa)
                            <tr >
                                <td >{{$empresa->rfc}}</td>
                                <td colspan="3">{{$empresa->razon_social}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >{{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->count(),0,"",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                                <td style="text-align: right" >${{number_format($empresa->cfd->where("tipo_comprobante","I")->where("rfc_emisor",$cambio->efos->rfc)->sum("total") - $empresa->cfd->where("tipo_comprobante","E")->where("rfc_emisor",$cambio->efos->rfc)->sum("total"),2,".",",")}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
</html>
