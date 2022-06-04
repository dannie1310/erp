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
            <div class="col-md-12">
                @if($token && count($solicitud->autorizacionesRequeridasPendientes))
                    <form id="frm_rechazar" action="/api/solicitud-pago-anticipado/{{$solicitud->id}}/rechazar" method="GET" style="display: inline;">
                        @csrf
                        <input type="hidden" name="access_token" value ="{{$token}}">
                    </form>
                @endif
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
        mounted() {
            this.cancelar();
        },
        methods:{

            cancelar: function(event){

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

