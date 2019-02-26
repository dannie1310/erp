<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_movimiento_bancario')" class="btn btn-app pull-right" >
            <i class="fa fa-plus"></i> Registrar Fondo de Garantía
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-edit" style="padding-right:3px"></i>Registrar Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">

                            <div class="row">
                                <!-- SubcontratoService -->
                                <div class="col-md-2">
                                    <label for="id_fondo_garantia">Subcontrato:</label>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <select
                                                class="form-control"
                                                name="id_subcontrato"
                                                data-vv-as="Subcontrato"
                                                id="id_fondo_garantia"
                                                v-model="id_subcontrato"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_subcontrato')}"
                                        >
                                            <option value>-- Subcontrato --</option>
                                            <option v-for="(item, index) in subcontratosSinFondo" :value="item.id">
                                                {{ item.subcontrato.numero_folio_format + ' [' + item.subcontrato.referencia + ']' }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_subcontrato')">{{ errors.first('id_subcontrato') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Porcentaje Retención -->
                                <div class="col-md-2">
                                     <label for="importe">% de Retención:</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <input
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                id="retencion"
                                                name="retencion"
                                                v-model="retencion"
                                                v-validate="{required: true, decimal: true}"
                                                data-vv-as="Retención"
                                                :class="{'is-invalid': errors.has('retencion')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('retencion')">{{ errors.first('retencion') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>
    export default {
        name: "fondo-garantia-create",
        data() {
            return {
                id_subcontrato : '',
                retencion : ''


            }
        },

        mounted() {
            this.getSubcontratosSinFondo()

        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.id_subcontrato = '';
                this.retencion = '';
                this.$validator.reset()
            },

            getSubcontratosSinFondo() {
                return [];
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            store() {
                return this.$store.dispatch('contratos/fondo-garantia/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                    })
            }
        },

        computed: {
            subcontratosSinFondo() {
                return this.$store.getters['contratos/fondo-garantia/fondosGarantia']
            },
        }
    }
</script>

<style scoped>
    .btn-primary {
        background-color: #00c0ef;
        border-color: #00acd6;
        color: #FFF;
    }
    button:checked{
        background-color: #5bc0de;
    }
    .btn-primary:hover {
        background-color: #5bc0de;
        border-color: #46b8da;
    }
</style>