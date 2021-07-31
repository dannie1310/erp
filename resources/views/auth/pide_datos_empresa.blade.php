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
        <div class="login-logo offset-2 centered">
            <img src="{{URL::asset('/img/logo_hc.png')}}" class="img-responsive img-rounded" width="70%">
        </div>
    </div>
    <form method="POST" @submit.prevent="enviar">
        <div class="login-box offset-2 centered">
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg"><b>Complementar los datos básicos de la empresa</b></p>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="rfc">Razón Social:</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="razon_social" class="form-control{{ $errors->has('razon_social') ? ' is-invalid' : '' }}" v-model="razon_social" value="{{ old('razon_social') }}" required autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rfc">RFC:</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="rfc" class="form-control{{ $errors->has('rfc') ? ' is-invalid' : '' }}" v-model="rfc" value="{{ old('rfc') }}" required autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="carta_terminos">Constancia de situación fiscal (pdf):</label>
                                <div class="input-group mb-3">
                                    <input type="file" id="constancia_situacion_fiscal"
                                           @change="onFileChange"
                                           name="constancia_situacion_fiscal"
                                           class="form-control{{ $errors->has('constancia_situacion_fiscal') ? ' is-invalid' : '' }}"
                                           placeholder="Constancia de Situación Fiscal"
                                           value="{{ old('constancia_situacion_fiscal') }}"
                                           required autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clave_nueva">Nueva Contraseña:</label>
                                <div class="input-group mb-3">
                                    <input type="password" name="clave_nueva" class="form-control{{ $errors->has('clave_nueva') ? ' is-invalid' : '' }}" v-model="clave_nueva"  value="{{ old('clave_nueva') }}" required autofocus>
                                    <div class="input-group-append">
                                        <span class="fas fa-lock input-group-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clave_nueva">Confirmar Contraseña:</label>
                                <div class="input-group mb-3">
                                    <input type="password" name="clave_confirmacion" v-model="clave_confirmacion" class="form-control{{ $errors->has('clave_confirmacion') ? ' is-invalid' : '' }}" required>
                                    <div class="input-group-append">
                                        <span class="fa fa-lock input-group-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class=" col-md-10">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" style="cursor:pointer" >
                                    <input class="form-check-input" type="checkbox" name="aviso_privacidad_aceptado" v-model="aviso_privacidad_aceptado" value="1" >
                                </label>
                            </div>
                            <b>He leído y acepto el <a v-on:click="abreAviso" href="#" style="text-decoration: underline"> Aviso de Privacidad</a></b>
                        </div>
                        <div  class="col-md-2">
                            <button type="submit" class="btn btn-warning btn-block btn-flat">Continuar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><div class="modal fade" ref="criterios_contraseña" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" ><i class="fa fa-info-circle"></i> Atención</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12"  v-if="cant_errores > 0">
                                La contraseña debe cumplir con los siguientes criterios:
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12"  v-if="cant_errores > 0">
                                <div class="input-group mb-3" v-for="error in errors">
                                    <i v-if="error.valido" class="fa fa-check" style="color:green"></i>
                                    <i v-else class="fa fa-times" style="color:red"></i>
                                    <b>@{{error.message}}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" ref="aviso_privacidad" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" ><i class="fa fa-info-circle"></i> Aviso de Privacidad</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p style="text-align: justify">
                                    Hermes Infraestructura S.A. de C.V., con domicilio ubicado en Paseo de la Reforma 383, Piso 8, Col. Cuauhtémoc, Alcaldía Cuauhtémoc, Ciudad de México,
                                    C.P. 06500, hará uso de sus datos personales, sensibles y patrimoniales, para el desarrollo de las siguientes finalidades: cumplimiento de
                                    la relación contractual generada como cliente o proveedor, establecimiento y cumplimiento de la relación laboral y prestaciones derivados de
                                    la misma; atención a solicitudes, quejas, dudas y/o comentarios relacionados con los servicios prestados por Hermes Infraestructura; para
                                    control estadísctico, publicitario, mercadológico o prospección comercial y/o laboral de Hermes Infraestructura, sus filiales, subsidiarias,
                                    afiliadas, empresas que tengan el control sobre sus acciones y/o terceros, así con quien se tengan contratos de colaboración en beneficio de
                                    sus colaboradores.
                                </p>
                                <p style="text-align: justify">
                                    Para mayor información acerca del tratamiento de sus datos personales, los derechos (ARCO) y procedimientos que podrían hacerse valer, Grupo Hermes,
                                    pone a su disposición el Aviso de Privacidad Integral en el Departamento de Datos Personales, ubicado en el domicilio enunciado previamente.
                                </p>

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label" style="cursor:pointer" >
                                    <input class="form-check-input" type="checkbox" name="aviso_privacidad_aceptado" v-model="aviso_privacidad_aceptado" value="1" >
                                </label>
                            </div>
                            <b>He leído y acepto el Aviso de Privacidad</b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" :disabled="aviso_privacidad_aceptado==0"><i class="fa fa-close"></i>Cerrar</button>
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
            razon_social:'',
            rfc:'',
            archivo_constancia:'',
            nombre_archivo_constancia:'',
            aviso_privacidad_aceptado : 0,
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
                        text: 'La nueva contraseña debe conincidir con la confirmación de contraseña.',
                        icon: "warning",
                        confirmButtonText: "Ok",
                    });
                }else if(this.aviso_privacidad_aceptado == 0){
                    $(this.$refs.aviso_privacidad).appendTo('body')
                    $(this.$refs.aviso_privacidad).modal('show');
                }else if(this.validacion_contrasenia()){
                    return new Promise((resolve, reject) => {
                        axios
                            .post('login', {
                                clave_confirmacion : this.clave_confirmacion,
                                clave_nueva : this.clave_nueva,
                                archivo_constancia : this.archivo_constancia,
                                nombre_archivo_constancia : this.nombre_archivo_constancia,
                                razon_social : this.razon_social,
                                rfc : this.rfc,
                                aviso_privacidad_aceptado : this.aviso_privacidad_aceptado,
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
                                        case (code === 444):
                                            swal({
                                                title: "Atención",
                                                text: message,
                                                icon: "warning",
                                                confirmButtonText: "Entendido",
                                                closeOnConfirm: true,
                                            }).then((value) => {
                                                window.location.href = 'login';
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
                }
            },
            abreAviso(){
                $(this.$refs.aviso_privacidad).appendTo('body')
                $(this.$refs.aviso_privacidad).modal('show');
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
                    $(this.$refs.criterios_contraseña).appendTo('body')
                    $(this.$refs.criterios_contraseña).modal('show');
                    return false;
                }
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;
                reader.onload = (e) => {
                    vm.archivo_constancia = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.nombre_archivo_constancia = files[0].name;
                this.createImage(files[0]);
            },
        }
    });
</script>
<style scoped>
    .login-box, .register-box{
        width: 600px;
    }
</style>
</html>
