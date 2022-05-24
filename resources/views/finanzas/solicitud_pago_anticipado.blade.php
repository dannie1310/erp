<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SAO-Solicitud de Autorización de Pago Anticipado</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    </head>
    <body>
    <div id="content">
        <br>
        @if($error)
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-info-circle"></i>{{$error}}
                    </div>

                </div>

            </div>
        @elseif($mensaje)
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-info-circle"></i>{{$mensaje}}
                    </div>

                </div>

            </div>
        @else
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="alert alert-primary" role="alert">
                        <i class="fa fa-info-circle"></i>Se le informa que {{$solicitud->usuarioRegistro->nombre_completo}} ha solicitado que se autorice el pago anticipado que se describe a continuación:
                    </div>

                </div>

            </div>
        @endif


        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card" >
                    <div class="card-header">
                        <h6>
                            <i class="fa fa-file-powerpoint"></i>Solicitud de Pago Anticipado {{$solicitud->solicitud_pago_anticipado->numero_folio_format}}
                            <form id="frm_pendientes" action="/api/solicitud-pago-anticipado" method="GET" style="display: inline;" class="pull-right">
                                @csrf
                                <input type="hidden" name="access_token" value ="{{$token}}">
                                <input type="hidden" name="scope" value ="autorizacionPendiente">
                                <button type="button" class="btn btn-secondary"  v-on:click="ir_pendientes">Ver Pendientes</button>
                            </form>
                        </h6>
                    </div>
                    <div class="card-body">


                    <div class="row" style="font-size: 16px">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Proyecto:
                                </label>
                                <div style="font-weight: bold">{{$solicitud->solicitud_pago_anticipado->obra->nombre}}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <label style="font-weight: normal">
                                        Proveedor / Contratista:
                                    </label>
                                    <div style="font-weight: bold">
                                        {{$solicitud->solicitud_pago_anticipado->empresa->razon_social}}
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Monto Solicitado:
                                </label>
                                <div style="font-weight: bold; text-align: right; color: #ff0000">
                                    {{$solicitud->solicitud_pago_anticipado->monto_format}} {{$solicitud->solicitud_pago_anticipado->moneda->nombre}}
                                </div>

                            </div>
                        </div>
                    </div>

                        <hr>
                        Datos de Solicitud de Pago Anticipado
                        <hr>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Folio:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->numero_folio_format}}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Fecha:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->fecha_format}}
                                </div>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Vencimiento:
                                </label>
                                <div style="font-weight: bold;">
                                    {{$solicitud->solicitud_pago_anticipado->fecha_vencimiento_format}}
                                </div>

                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Monto:
                                </label>
                                <div style="font-weight: bold; text-align: right">
                                    {{$solicitud->solicitud_pago_anticipado->monto_format}} {{$solicitud->solicitud_pago_anticipado->moneda->nombre}}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label style="font-weight: normal">
                                    Motivo:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->observaciones}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    @if($solicitud->solicitud_pago_anticipado->transaccionAntecedenteSinGlobalScope)
                            <hr>
                            Datos de Transacción Antecedente
                            <hr>

                        <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Fecha:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->transaccionAntecedenteSinGlobalScope->fecha_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Folio:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->transaccion_antecedente_txt}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Monto:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->transaccionAntecedenteSinGlobalScope->monto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label style="font-weight: normal">
                                    Observaciones:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud->solicitud_pago_anticipado->transaccion_antecedente_observaciones}}
                                </div>
                            </div>
                        </div>
                    </div>

                    @endif

                    </div>
                    @if($token && count($solicitud->autorizacionesRequeridasPendientes))
                    <div class="card-footer">
                        <div class="pull-right">
                            <form id="frm_autorizar" action="/api/solicitud-pago-anticipado/{{$solicitud->id}}/autorizar" method="GET" style="display: inline;"  >
                                @csrf
                                <input type="hidden" name="access_token" value ="{{$token}}">
                                <button type="button" class="btn btn-danger" v-on:click="autorizar"><i class="fa fa-thumbs-o-up"></i>Autorizar</button>
                            </form>
                            <form id="frm_rechazar" action="/api/solicitud-pago-anticipado/{{$solicitud->id}}/rechazar" method="GET" style="display: inline;">
                                @csrf
                                <input type="hidden" name="access_token" value ="{{$token}}">
                                <button type="button" class="btn btn-warning"  v-on:click="cancelar"><i class="fa fa-thumbs-o-down"></i>Rechazar</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>


    </div>
    </body>
</html>
<script>
    new Vue({
        el:'#content',
        data:{
            id : '',
        },
        methods:{
            ir_pendientes:function(event){
                event.preventDefault();
                $("#frm_pendientes").submit();
            }
            ,
            autorizar:function(event){
                event.preventDefault();
                return new Promise((resolve, reject) => {
                    swal({
                        title: "Autorizar solicitud de pago",
                        text: "¿Está seguro de que desea autorizar que la solicitud de pago se incluya en la remesa del proyecto?",
                        icon: "warning",
                        buttons: {
                            cancel: {
                                text: 'Cancelar',
                                visible: true
                            },
                            confirm: {
                                text: 'Si, Autorizar',
                                closeModal: false,
                            }
                        },
                        dangerMode: true,
                    })
                        .then((value) => {
                            if (value) {
                                $("#frm_autorizar").submit();
                            }
                        });
                });
            }
            ,
            cancelar: function(event){
                event.preventDefault();
                return new Promise((resolve, reject) => {
                    swal({
                        title: "Rechazar solicitud de pago",
                        text: "¿Está seguro de rechazar la solicitud de pago?",
                        dangerMode: true,
                        icon: "info",
                        content: {
                            element: "input",
                            attributes: {
                                placeholder: "Motivo de Rechazo",
                                type: "text",
                            },
                        },
                        buttons: [
                            'Cancelar',
                            {
                                text: "Si, Rechazar",
                                closeModal: false,
                            }
                        ]
                    })
                        .then((value) => {
                            if (value) {
                                $("<input type='hidden' value='"+value+"' />")
                                    .attr("id", "motivo")
                                    .attr("name", "motivo")
                                    .appendTo("#frm_rechazar");

                                $("#frm_rechazar").submit();
                            } else {
                                swal("Ingrese el motivo de rechazo de la transacción.",{icon: "error"});
                            }
                        });
                });
            }
        },
    });

</script>

