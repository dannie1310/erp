<template>
    <div :style="margin" id="r" class="row justify-content-center">
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div :style="margin" class="card">
                <div class="card-body">
                    <span>
                        <span class="text-center" v-if="verified">
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
                         <button type="button" @click="closeWindow" class="btn btn-danger pull-right">Cancelar</button>
                    </span>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "google-auth",
        data() {
            return {
                valid: false,
                verified: this.$router.currentRoute.query.verified == 'false' ? false : true
            }
        },

        mounted() {
            this.init();
        },

        methods: {
            init() {
                $('.pincode-input-container').remove();

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
                                            // report succees
                                            opener.postMessage({ result: value }, location.origin);
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

            closeWindow() {
                window.close();
            }
        },

        computed: {
            margin() {
                return {
                    'margin-top': this.verified ? '25px' : '0px'
                }
            }
        }
    }
</script>
<style scoped>
    #r {
        margin: auto
    }

</style>
