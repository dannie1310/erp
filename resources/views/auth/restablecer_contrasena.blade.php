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
</head>
<body>
    <div id="content">
        <div class="row">
            <div class="login-logo offset-4 centered">
                <img src="{{URL::asset('/img/logo_hc.png')}}" class="img-responsive img-rounded" width="70%">
            </div>
        </div>
        <diV class="login-box offset-4 centered">
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg"><i class="fa fa-refresh" ></i><b> ¿Olvidaste tu contraseña?</b></p>

                    <form method="POST" @submit.prevent="enviar">
                        <div class="input-group mb-3">
                            <input type="text" name="usuario" v-model="usuario" class="form-control{{ $errors->has('usuario') ? ' is-invalid' : '' }}" placeholder="Usuario"  value="{{ old('usuario') }}"  required autofocus>
                            <div class="input-group-append">
                                <span class="fas fa-user input-group-text"></span>
                            </div>
                            @if ($errors->has('usuario'))
                                <span class="invalid-feedback" role="alert">
                                    {{ $errors->first('usuario') }}
                                </span>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" name="correo" v-model="correo" class="form-control{{ $errors->has('correo') ? ' is-invalid' : '' }}" placeholder="Correo" required>
                            <div class="input-group-append">
                                <span class="fa fa-envelope input-group-text"></span>
                            </div>
                            @if ($errors->has('correo'))
                                <span class="invalid-feedback" role="alert">
                                    {{ $errors->first('correo') }}
                                </span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning btn-block btn-flat">Restablecer Contraseña</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    new Vue({
        el:'#content',
        data:{
            usuario : '',
            correo : '',
        },
        methods:{
            enviar: function(event){
                event.preventDefault();
                    return new Promise((resolve, reject) => {
                        axios
                            .post('login', {
                                usuario : this.usuario,
                                correo : this.correo
                            })
                            .then(r => r.data)
                            .then(data => {
                                swal({
                                    title: "Contraseña Provisional Enviada",
                                    text: "Se envio por correo la contraseña provisional.",
                                    icon: "success",
                                    buttons: {
                                        confirm: {
                                            text: 'Ok',
                                            closeModal: false,
                                        }
                                    }
                                }).then((value) => {
                                    localStorage.clear();
                                    window.location.href = 'login';
                                });
                            })
                            .catch(error => {
                                if (!error.response) {
                                    alert('NETWORK ERROR')
                                } else {
                                    swal({
                                        title: "¡Error!",
                                        text: error.response.data.message,
                                        icon: "error"
                                    });

                                }
                            })
                    })
                }
            },
    });
</script>
</html>
