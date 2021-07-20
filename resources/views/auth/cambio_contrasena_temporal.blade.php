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
        function validate(){
            if(!document.getElementById("clave_nueva").value==document.getElementById("clave_confirmacion").value)alert("Passwords do no match");
            return document.getElementById("clave_nueva").value==document.getElementById("clave_confirmacion").value;
            return false;
        }
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
                    <p class="login-box-msg"><b>Actualización de Contraseña</b></p>
                    <div class="input-group mb-3">
                        <input type="password" name="clave_nueva" class="form-control{{ $errors->has('clave_nueva') ? ' is-invalid' : '' }}" v-model="clave_nueva" placeholder="Contraseña Nueva" value="{{ old('clave_nueva') }}" required autofocus>
                        <div class="input-group-append">
                            <span class="fas fa-lock input-group-text"></span>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="clave_confirmacion" v-model="clave_confirmacion" class="form-control{{ $errors->has('clave_confirmacion') ? ' is-invalid' : '' }}" placeholder="Confirmar Contraseña" required>
                        <div class="input-group-append">
                            <span class="fa fa-lock input-group-text"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning btn-block btn-flat">Actualizar Contraseña</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-box offset-4 centered" v-if="cant_errores > 0">
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">La contraseña debe cumplir con los siguientes criterios:</p>
                    <div class="input-group mb-3" v-for="error in errors">
                        <i v-if="error.valido" class="fa fa-check" style="color:green"></i>
                        <i v-else class="fa fa-times" style="color:red"></i>
                        <b>@{{error.message}}</b>
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
            clave_nueva:'',
            clave_confirmacion:'',
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
                if(this.clave_nueva != this.clave_confirmacion){
                    swal({
                        title: "Actualización de Contraseña",
                        text: 'La contraseña nueva debe conincidir con la confirmación.',
                        icon: "warning",
                        confirmButtonText: "Ok",
                    });
                }else if(this.validacion_contrasenia()){
                    return new Promise((resolve, reject) => {
                        axios
                            .post('login', { clave_confirmacion:this.clave_confirmacion, clave_nueva:this.clave_nueva})
                            .then(r => r.data)
                            .then(data => {
                                swal({
                                    title: "Actualizar Contraseña",
                                    text: "Contraseña Actualizada Correctamente.",
                                    icon: "success",
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: true,
                                }).then((value) => {
                                    window.location.href = data;
                                });
                            })
                            .catch(error => {
                                console.log(error);
                            })
                        })
                }
            },
            validacion_contrasenia(){
                let self = this;
                self.errors = [];
                self.cant_errores = 0;
                let errors = [];
                let cant = 0;
                self.rules.forEach(function(condition){
                    if (!condition.regex.test(self.clave_nueva)) {
                        cant = cant + 1;
                        errors.push({ message:condition.message, valido:false})
				    }else{
                        errors.push({ message:condition.message, valido:true})
                    }
                })
                self.errors = errors;
                self.cant_errores = cant;
                if (cant === 0) {
				    return true;
                } else {
                    return false;
                }
            }
        }
    });
</script>
</html>
