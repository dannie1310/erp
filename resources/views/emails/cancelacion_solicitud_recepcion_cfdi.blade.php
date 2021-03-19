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
        Se le informa que {{$solicitud->usuarioCancelo->nombre_completo}} ha cancelado una solicitud de recepción de CFDI para su proyecto.
        <div class="card" >
            <div class="card-header">
                <h5>Datos de la Solicitud de Recepción</h5>
            </div>
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Indetificador:</label>
                                {{$solicitud->id}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Folio para proveedor:</label>
                                {{$solicitud->numero_folio}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                {{$solicitud->base_datos}} ({{$solicitud->obra->nombre}})
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Contacto HI:</label>
                                {{$solicitud->contacto}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Correo para enviar notificaciones:</label>
                                {{$solicitud->correo_notificaciones}}
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
        <hr>
        <div class="card" >
            <div class="card-header">
                <h5>Datos del CFDI</h5>
            </div>
            <div class="card-body">
                <span>

                    <div class="row">
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
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Tipo:</label>
                                {{$solicitud->cfdi->tipo_comprobante}}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label >UUID:</label>
                                {{$solicitud->cfdi->uuid}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                            <label >Empresa:</label>
                                {{$solicitud->cfdi->empresa->razon_social}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >RFC:</label>
                                {{$solicitud->cfdi->empresa->rfc}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Descuento:</label>
                                {{$solicitud->cfdi->descuento_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Retenidos:</label>
                                {{$solicitud->cfdi->total_impuestos_retenidos_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Trasladados:</label>
                                {{$solicitud->cfdi->total_impuestos_trasladados_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Total:</label>
                                {{$solicitud->cfdi->total_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Moneda:</label>
                                {{$solicitud->cfdi->moneda}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Tipo de Cambio:</label>
                                {{$solicitud->cfdi->tipo_cambio}}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Conceptos del CFDI</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th class="no_parte">Clave Producto / Servicio</th>
                                        <th>Descripción</th>
                                        <th>Clave Unidad</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Descuento</th>
                                        <th>Valor Unitario</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($solicitud->cfdi->conceptos as $concepto)
                                        <tr >
                                            <td>{{1}}</td>
                                            <td>{{$concepto->clave_prod_serv}}</td>
                                            <td>{{$concepto->descripcion}}</td>
                                            <td>{{$concepto->clave_unidad}}</td>
                                            <td>{{$concepto->unidad}}</td>
                                            <td style="text-align: right">{{$concepto->cantidad_format}}</td>
                                            <td style="text-align: right">{{$concepto->descuento_format}}</td>
                                            <td style="text-align: right">{{$concepto->valor_unitario_format}}</td>
                                            <td style="text-align: right">{{$concepto->importe_format}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </div>
</html>
