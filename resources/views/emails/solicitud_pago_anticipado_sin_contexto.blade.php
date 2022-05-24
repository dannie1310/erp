<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SAO-Solicitud de Autorización de Pago Anticipado</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>

    </head>
    <div id="app">
        Estimado {{$nombre_usuario}} se le informa que {{$solicitud->usuarioRegistro->nombre_completo}} ha solicitado que se autorice el pago anticipado que se describe a continuación:
        <br>
        <br>
        <div class="card" >
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                <b>{{$solicitud->solicitud_pago_anticipado->obra->nombre}}</b>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proveedor / Contratista:</label>
                                <b>{{$solicitud->solicitud_pago_anticipado->empresa->razon_social}}</b>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Monto Solicitado:</label>
                                <b>{{$solicitud->solicitud_pago_anticipado->monto_format}}</b>
                            </div>
                        </div>
                        <br>
                        <b>
                        Solicitud de Pago<br>
                        --------------------------------------
                        </b>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Fecha:</label>
                                {{$solicitud->solicitud_pago_anticipado->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio:</label>
                                {{$solicitud->solicitud_pago_anticipado->numero_folio_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Monto:</label>
                                <b>{{$solicitud->solicitud_pago_anticipado->monto_format}}</b>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud->solicitud_pago_anticipado->observaciones}}
                            </div>
                        </div>
                    </div>
                    <br>

                    @if($solicitud->solicitud_pago_anticipado->transaccionAntecedente)
                        <b>
                        Transacción Antecedente<br>
                        --------------------------------------
                        </b>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Fecha:</label>
                                {{$solicitud->solicitud_pago_anticipado->transaccionAntecedente->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio:</label>
                                {{$solicitud->solicitud_pago_anticipado->transaccion_antecedente_txt}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Monto:</label>
                                {{$solicitud->solicitud_pago_anticipado->transaccionAntecedente->monto_format}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud->solicitud_pago_anticipado->transaccion_antecedente_observaciones}}
                            </div>
                        </div>
                    </div>

                    @endif
                    @if($token)
                        <br>
                        <a href="http://{{$_SERVER['SERVER_NAME']}}:{{$_SERVER['SERVER_PORT']}}/api/solicitud-pago-anticipado/{{$solicitud->id}}/autorizar?access_token={{$token}}">[AUTORIZAR]</a>
                        <a href="http://{{$_SERVER['SERVER_NAME']}}:{{$_SERVER['SERVER_PORT']}}/api/solicitud-pago-anticipado/{{$solicitud->id}}/pide-motivo-rechazo?access_token={{$token}}">[RECHAZAR]</a>
                        <a href="http://{{$_SERVER['SERVER_NAME']}}:{{$_SERVER['SERVER_PORT']}}/api/solicitud-pago-anticipado?access_token={{$token}}">[VER PENDIENTES]</a>
                    @endif

                </span>
            </div>
        </div>

    </div>
</html>
