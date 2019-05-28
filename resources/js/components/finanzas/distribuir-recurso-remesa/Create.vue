<template>
    <span>
        <button  @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Distribuir
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR DISTRIBUCIÓN DE RECURSOS AUTORIZADOS DE REMESA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_remesa">Remesas Autorizada: </label>
                                        <select
                                                type="text"
                                                name="id_remesa"
                                                data-vv-as="Remesa Autorizada"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_remesa"
                                                v-model="id_remesa"
                                                :class="{'is-invalid': errors.has('id_remesa')}"
                                        >
                                            <option value>-- Seleccione una Remesa --</option>
                                            <option v-for="rem in remesas" :value="rem.id">{{ rem.folio }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_remesa')">{{ errors.first('id_remesa') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "distribuir-recurso-remesa-create",
        data() {
            return {
                id_remesa : '',
                remesas : [],
                cargando: false
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');

                this.id_remesa = '';
                this.remesas = [];

                this.$validator.reset()
                this.cargando = false;

            },

            getRemesa() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/remesa/index', {
                    params: {}
                })
                    .then(data => {
                        this.remesas = data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
        },
        watch: {

        }
    }
</script>
<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>