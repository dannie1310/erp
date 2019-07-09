<template>
    <div class="modal fade" ref="modal2fa" tabindex="-1" role="dialog" aria-labelledby="2faModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="2faModalLabel">Autenticación de 2 pasos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>Descarga la APP desde <b><a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Google Authenticator</a></b></li>
                        <li>Dentro la APP selecciona <b>COMENZAR</b></li>
                        <li>Seleccione <b>Escanear código de barras</b> y escanee el siguiente código: </li>
                    </ul>
                    <br>
                    <center>
                        <img :src="'/api/SEGURIDAD_ERP/google-2fa/qr?access_token=' + this.$session.get('jwt')" class="img-thumbnail">
                    </center>
                    <br>
                    <ul>
                        <li>Una vez escaneado, ingrese el <b>código de verificación</b> que le proporcionó la APP</li>
                    </ul>
                    <br>
                    <center>
                        <input type="text" ref="codeInput">
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "two-factor-auth-modal",
        data() {
            return {
                cargando: false,
            }
        },

        mounted() {
            $(this.$refs.modal2fa).on('hidden.bs.modal', () => {
                this.cargando = false;
            });

            $(this.$refs.codeInput).pincodeInput({
                inputs: 6,
                hidedigits: false,
                complete:(value, e, errorElement) => {
                    this.checkCode(value)
                        .then(data => {
                            if (data.valid) {
                                $(errorElement).html("");
                                swal("Código verificado correctamente", {
                                    icon: "success",
                                    timer: 1500,
                                    buttons: false
                                })
                                    .then(() => {
                                        $(this.$refs.codeInput).pincodeInput().data('plugin_pincodeInput').clear();
                                        $(this.$refs.modal2fa).modal('hide');
                                    })
                            } else {
                                $(this.$refs.codeInput).pincodeInput().data('plugin_pincodeInput').clear();
                                $(this.$refs.codeInput).pincodeInput().data('plugin_pincodeInput').focus();
                                $(errorElement).html("El código que ingresó no es válido");
                            }
                        })
                }
            });
        },

        methods: {
            init() {
                this.cargando = true;
                axios
                    .get('/api/SEGURIDAD_ERP/google-2fa/isVerified')
                    .then(r => r.data)
                    .then(data => {
                        if (data.verified == true) {
                            swal("Ya se ha habilitado la autenticación de dos pasos", {
                                icon: "info",
                                buttons: false
                            });
                        } else {
                            $(this.$refs.modal2fa).modal('show');
                            $(this.$refs.codeInput).pincodeInput().data('plugin_pincodeInput').focus();
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            checkCode(code) {
                return new Promise((resolve, reject) => {
                    axios.post('/api/SEGURIDAD_ERP/google-2fa/check', {
                        code: code
                    })
                        .then(r => r.data)
                        .then(data => {
                            resolve(data)
                        })
                        .catch(error => {
                            reject(error)
                        });
                });
            }
        }
    }
</script>