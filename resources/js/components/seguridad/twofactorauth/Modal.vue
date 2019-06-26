<template>
    <span>
        <!-- Button trigger modal -->
        <button type="button" @click="init" class="btn btn-primary" :disabled="cargando">
            <span v-if="cargando">
                <i class="fa fa-spin fa-spinner"></i>
            </span>
            <span v-else>
                Autenticación de 2 Pasos
            </span>
        </button>

        <!-- Modal -->
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
                        <img :src="'/api/SEGURIDAD_ERP/google-2fa/qr?access_token=' + this.$session.get('jwt')" class="img-thumbnail">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "two-factor-auth-modal",
        data() {
            return {
                cargando: false,
                qr: null
            }
        },

        mounted() {
            $(this.$refs.modal2fa).on('hidden.bs.modal', () => {
                this.cargando = false;
            });
        },

        methods: {
            init() {
                //  this.cargando = true;
                $(this.$refs.modal2fa).modal('show');
                /*axios
                    .get('/api/SEGURIDAD_ERP/google-2fa/isVerified')
                    .then(r => r.data)
                    .then(data => {
                        if (data.verified) {
                            axios.get('/api/SEGURIDAD_ERP/google-2fa/qr')
                                .then(r => r.data)
                                .then(data => {
                                    this.qr = `data:image/png;base64,${data.qr}`;
                                    $(this.$refs.modal2fa).modal('show');
                                })

                        } else {
                            alert('not verified')
                        }
                    })*/
            }
        }
    }
</script>