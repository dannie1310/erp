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
        Estimado {{$nombre_usuario}} se le informa que {{$usuario_registro}} ha solicitado que se autorice el pago anticipado que se describe a continuación:
        <br>
        <br>
        <div class="card" >
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                <b>{{$solicitud->obra->nombre}}</b>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proveedor / Contratista:</label>
                                <b>{{$solicitud->empresa->razon_social}}</b>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Monto Solicitado:</label>
                                <b>{{$solicitud->monto_format}}</b>
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
                                {{$solicitud->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio:</label>
                                {{$solicitud->numero_folio_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Monto:</label>
                                <b>{{$solicitud->monto_format}}</b>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud->observaciones}}
                            </div>
                        </div>
                    </div>
                    <br>

                    @if($solicitud->transaccionAntecedente)
                        <b>
                        Transacción Antecedente<br>
                        --------------------------------------
                        </b>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Fecha:</label>
                                {{$solicitud->transaccionAntecedente->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio:</label>
                                {{$solicitud->transaccion_antecedente_txt}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Monto:</label>
                                {{$solicitud->transaccionAntecedente->monto_format}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud->transaccion_antecedente_observaciones}}
                            </div>
                        </div>
                    </div>

                    @endif
                    @if($token)
                        <br>
                        <a href="http://{{$_SERVER['SERVER_NAME']}}:{{$_SERVER['SERVER_PORT']}}/api/solicitud-pago-anticipado/{{$solicitud->transaccionGeneral->id}}?access_token={{$token}}">[AUTORIZAR / RECHAZAR]</a>
                    @endif

                </span>
            </div>
        </div>

    </div>
</html>
