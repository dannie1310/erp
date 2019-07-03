<template>
    <div ref="modal" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <span v-if="cargando">
                        <center>
                            <i class="fa fa-spin fa-2x fa-spinner"></i>
                        </center>
                    </span >
                    <span v-else>
                        <span v-if="verified">
                            <h5>Ingrese código de verificación de Google Auth</h5>
                        </span>
                        <span v-else>
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
                        </span>
                        <center>
                            <input type="text" ref="code">
                        </center>
                         <button type="button" class="btn btn-light pull-right" data-dismiss="modal" >Cancelar</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "google-auth",
        props: ['value'],
        data() {
            return {
                cargando: true,
                valid: false,
                verified: null
            }
        },

        mounted() {

            $(this.$refs.modal)
                .on('hidden.bs.modal', () => {
                    $(this.$refs.code).pincodeInput().data('plugin_pincodeInput').clear();
                    if (! this.valid) {
                        this.$emit('cancel');
                    }
                })
        },

        methods: {

            init() {
                $('.pincode-input-container').remove();
                $(this.$refs.modal).modal('show');
                this.checkVerified()
                    .finally(() => {
                        $(this.$refs.code).pincodeInput({
                            inputs : 6,
                            hidedigits: false,
                            complete:(value, e, errorElement) => {
                                this.checkCode(value)
                                    .then(data => {
                                        if (data.valid) {
                                            this.valid = true;
                                            $(errorElement).html("");
                                            swal("Verificación correcta", {
                                                icon: "success",
                                                timer: 1500,
                                                buttons: false
                                            })
                                                .then(() => {
                                                    $(this.$refs.code).pincodeInput().data('plugin_pincodeInput').clear();
                                                    $(this.$refs.modal).modal('hide');
                                                    this.$emit('success', value);
                                                })
                                        } else {
                                            $(this.$refs.code).pincodeInput().data('plugin_pincodeInput').clear();
                                            $(this.$refs.code).pincodeInput().data('plugin_pincodeInput').focus();
                                            $(errorElement).html("El código que ingresó no es válido");
                                        }
                                    });
                            }
                        });
                        $(this.$refs.code).pincodeInput().data('plugin_pincodeInput').focus();


                    });
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
            },

            checkVerified() {
                this.cargando = true;
                return new Promise((resolve, reject) => {
                    axios
                        .get('/api/SEGURIDAD_ERP/google-2fa/isVerified')
                        .then(r => r.data)
                        .then(data => {
                            this.verified = data.verified;
                        })
                        .finally(() => {
                            this.cargando = false;
                            resolve();
                        })
                });
            }
        }
    }
</script>

<style scoped>
    .modal {
        text-align: center;
    }
    @media screen and (min-width: 768px) {
        .modal:before {
            display: inline-block;
            vertical-align: middle;
            content: " ";
            height: 100%;
        }
    }
    .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
    }
</style>
