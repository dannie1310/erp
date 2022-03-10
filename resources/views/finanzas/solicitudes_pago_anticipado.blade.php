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
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card" >
                    <div class="card-header">
                        <h6>
                            <i class="fa fa-list"></i>Solicitudes de Pago Anticipado Pendientes de Autorizar
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-striped table-bordered table-hover" style="font-size: 11px">
                                    <thead>
                                        <tr>
                                            <th class="th_index_corto">
                                                #
                                            </th>
                                            <th>
                                                Proyecto
                                            </th>
                                            <th class="th_c80">
                                                Número Folio
                                            </th>
                                            <th class="th_c80">
                                                Fecha
                                            </th>
                                            <th>
                                                Proveedor
                                            </th>
                                            <th class="th_c100">
                                                RFC
                                            </th>
                                            <th class="th_c100">
                                                Moneda
                                            </th>
                                            <th class="th_c100">
                                                Monto
                                            </th>
                                            <th class="th_index_corto">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($solicitudes as $i=>$solicitud)
                                    <tr>
                                        <td>
                                            {{$i + 1}}
                                        </td>
                                        <td>
                                            {{$solicitud->proyecto}}
                                        </td>
                                        <td style="text-align: center">
                                            {{$solicitud->numero_folio_format}}
                                        </td>
                                        <td style="text-align: center">
                                            {{$solicitud->fecha_format}}
                                        </td>
                                        <td>
                                            {{$solicitud->razon_social}}
                                        </td>
                                        <td>
                                            {{$solicitud->rfc}}
                                        </td>
                                        <td>
                                            {{$solicitud->moneda}}
                                        </td>
                                        <td style="text-align: right">
                                            {{$solicitud->monto_format}}
                                        </td>
                                        <td>
                                            <form id="frm_autorizar" action="/api/solicitud-pago-anticipado/{{$solicitud->id}}" method="GET" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="access_token" value ="{{$token}}">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" style="padding: 2px 5px 2px 8px" ><i class="fa fa-angle-right"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>


                                </table>

                            </div>
                        </div>
                    </div>

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

