<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_concepto')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i> Registrar Cuenta
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE CONCEPTO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <div class="form-group row error-content">
                                    <label for="id_concepto" class="col-sm-2 col-form-label">Concepto</label>
                                    <div class="col-sm-10">
                                        <concepto-select
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                v-validate="{required: true}"
                                                id="id_concepto"
                                                v-model="id_concepto"
                                                :error="errors.has('id_concepto')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="false"
                                        ></concepto-select>
                                        <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row error-content">
                                    <label for="cuenta" class="col-sm-2 col-form-label">Cuenta</label>
                                    <div class="col-sm-10">
                                        <input
                                                type="text"
                                                name="cuenta"
                                                data-vv-as="Cuenta"
                                                v-validate="{required: true, regex: datosContables}"
                                                class="form-control"
                                                v-mask="{regex: datosContables}"
                                                id="cuenta"
                                                placeholder="Cuenta"
                                                v-model="cuenta"
                                                :class="{'is-invalid': errors.has('cuenta')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
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
    import ConceptoSelect from "../../cadeco/concepto/Select";
    export default {
        name: "cuenta-concepto-create",
        components: {ConceptoSelect},
        data() {
            return {
                id_concepto: '',
                cuenta: '',
                cargando: false
            }
        },

        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');

                this.id_concepto = '';
                this.cuenta = '';

                this.$validator.reset()
                this.$refs.conceptoSelect.getRootNodes()
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-concepto/store', this.$data)
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
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