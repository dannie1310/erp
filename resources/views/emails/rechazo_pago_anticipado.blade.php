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
        Estimado {{$solicitud_pago_autorizacion->usuarioRegistro->nombre_completo}} se le informa que se ha rechazado que se solicite en remesa el pago anticipado que se describe a continuación:
        <br>
        <br>
        <div class="card" >
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                <b>{{$solicitud_pago_anticipado->obra->nombre}}</b>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proveedor / Contratista:</label>
                                <b>{{$solicitud_pago_anticipado->empresa->razon_social}}</b>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Monto Solicitado:</label>
                                <b>{{$solicitud_pago_anticipado->monto_format}}</b>
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
                                {{$solicitud_pago_anticipado->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio:</label>
                                {{$solicitud_pago_anticipado->numero_folio_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Monto:</label>
                                <b>{{$solicitud_pago_anticipado->monto_format}}</b>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud_pago_anticipado->observaciones}}
                            </div>
                        </div>
                    </div>
                    <br>

                    @if($solicitud_pago_anticipado->transaccionAntecedenteSinGlobalScope)
                        <b>
                        Transacción Antecedente<br>
                        --------------------------------------
                        </b>

                        <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Fecha:</label>
                                {{$solicitud_pago_anticipado->transaccionAntecedenteSinGlobalScope->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio:</label>
                                {{$solicitud_pago_anticipado->transaccion_antecedente_txt}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Monto:</label>
                                {{$solicitud_pago_anticipado->transaccionAntecedenteSinGlobalScope->monto_format}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud_pago_anticipado->transaccion_antecedente_observaciones}}
                            </div>
                        </div>
                    </div>
                    @endif
                    <br>
                    <b style="color: #f00">
                        Motivo de Rechazo:<br>
                        --------------------------------------
                    </b>
                    <div class="row" style="color: #f00">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                {{$motivo_rechazo}}
                            </div>
                        </div>
                    </div>
                    <br>
                </span>
            </div>
        </div>

    </div>
</html>
