<!DOCTYPE html>
<html lang="es">
<head>
    <link href="/img/company-icon.png" rel="shortcut icon" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
    <style>
        #content {
            width: 100%; height: 100%;
            background-color: #d2d6de;
            position: absolute; top: 0; left: 0;
        }
    </style>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

    </script>
</head>
<body>
<div id="content">
    <div class="row">
        <div class="login-logo offset-4 centered">
            <img src="{{URL::asset('/img/logo_hc.png')}}" class="img-responsive img-rounded" width="70%">
        </div>
    </div>
    <form method="POST" @submit.prevent="enviar">
        <div class="login-box offset-4 centered">
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg"><i class="fa fa-retweet" ></i><b>Restablecer Contraseña</b></p>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning btn-block btn-flat">Restablecer Contraseña</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
<script>
    new Vue({
        el:'#content',
        data:{
            clave_nueva : '',
            clave_confirmacion : '',
            razon_social : '',
            rfc : '',
            archivo_constancia : '',
            nombre_archivo_constancia : '',
            rules: [
                { message:'Un caracter especial (->@#)', valido:true, regex:/[->@#]+/ },
                { message:"Al menos una letra mayúscula", valido:true,  regex:/[A-Z]+/ },
                { message:"Longitud mínima de 8 caracteres", valido:true, regex:/.{8,}/ },
                { message:"Al menos un número", valido:true, regex:/[0-9]+/ }
            ],
            errors:[],
            cant_errores:0
        },
        methods:{
            enviar: function(event){
                event.preventDefault();
              /*  if(this.clave_nueva != this.clave_confirmacion){
                    swal({
                        title: "Actualización de Contraseña",
                        text: 'La contraseña nueva debe conincidir con la confirmación de contraseña.',
                        icon: "warning",
                        confirmButtonText: "Ok",
                    });
                }else if(this.validacion_contrasenia()){
                    return new Promise((resolve, reject) => {
                        axios
                            .post('login', {
                                clave_confirmacion:this.clave_confirmacion,
                                clave_nueva:this.clave_nueva
                            })
                            .then(r => r.data)
                            .then(data => {
                                swal({
                                    title: "Actualizar Contraseña",
                                    text: "Contraseña Actualizada Correctamente.",
                                    icon: "success",
                                    timer: 1500,
                                    buttons: false
                                }).then((value) => {
                                    window.location.href = data;
                                });
                            })
                            .catch(error => {
                                if (!error.response) {
                                    alert('NETWORK ERROR')
                                } else {
                                    const code = error.response.status
                                    const message = error.response.data.message
                                    const originalRequest = error.config;
                                    switch (true) {
                                        case (code === 401 && !originalRequest._retry):
                                            swal({
                                                title: "La sesión ha expirado",
                                                text: "Volviendo a la página de Inicio de Sesión",
                                                icon: "error",
                                            }).then((value) => {
                                                localStorage.clear();
                                                window.location.href = 'login';
                                            })
                                            break;
                                        case (code === 500):
                                            swal({
                                                title: "¡Error!",
                                                text: message,
                                                icon: "error"
                                            });
                                        case (code === 400):
                                            swal({
                                                title: "Atención",
                                                text: message,
                                                icon: "warning"
                                            });
                                            break;
                                        default:
                                            swal({
                                                title: "¡Error!",
                                                text: message,
                                                icon: "error"
                                            });
                                    }
                                }
                            })
                    })
                }*/
            },
        }
    });
</script>
</html>
