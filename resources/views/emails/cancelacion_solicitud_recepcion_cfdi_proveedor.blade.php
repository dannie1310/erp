<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SAO-Solicitud de Recepción de CFDI</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>

    </head>
    <div id="app">
        Se le informa que se ha cancelado la solicitud de recepción de CFDI
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Total:</label>
                                <b>{{$solicitud->cfdi->total_format}}</b>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Emisión:</label>
                                {{$solicitud->cfdi->fecha_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Serie y Folio:</label>
                                {{$solicitud->cfdi->serie}} {{$solicitud->cfdi->folio}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio de Solicitud:</label>
                                {{$solicitud->numero_folio}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Contacto HI:</label>
                                {{$solicitud->contacto}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label >Observaciones:</label>
                                {{$solicitud->comentario}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Canceló:</label>
                            {{$solicitud->usuarioCancelo->nombre_completo}}
                        </div>
                        <div class="col-md-3">
                            <label>
                                Fecha / Hora Cancelación:
                            </label>
                            {{$solicitud->fecha_hora_cancelacion_format}}
                        </div>
                        <div class="col-md-3">
                            <label>
                                Motivo Cancelación:
                            </label>
                            {{$solicitud->motivo_cancelacion}}
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </div>
</html>
