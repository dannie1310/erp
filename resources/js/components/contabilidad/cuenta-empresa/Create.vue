<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_empresa')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Cuenta
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE EMPRESA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Empresa</label>
                                        <empresa-select
                                                name="id_empresa"
                                                placeholder="-- Banco --"
                                                id="id_empresa"
                                                data-vv-as="Empresa"
                                                v-validate="{required: true}"
                                                v-model="id_empresa"
                                                :error="errors.has('id_empresa')">
                                        ></empresa-select>
                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_tipo_cuenta_empresa">Tipo de Cuenta</label>
                                        <select
                                                type="text"
                                                name="id_tipo_cuenta_empresa"
                                                data-vv-as="Tipo de Cuenta"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_tipo_cuenta_empresa"
                                                v-model="id_tipo_cuenta_empresa"
                                                :class="{'is-invalid': errors.has('id_tipo_cuenta_empresa')}"
                                        >
                                            <option value>-- Tipo de Cuenta --</option>
                                            <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_cuenta_empresa')">{{ errors.first('id_tipo_cuenta_empresa') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="cuenta">Cuenta</label>
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
                                                :class="{'is-invalid': errors.has('cuenta')}">
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
    import EmpresaSelect from "../../cadeco/empresa/Select";
    export default {
        name: "cuenta-empresa-create",
        components: {EmpresaSelect},
        data() {
            return {
                id_empresa: '',
                cuenta: '',
                id_tipo_cuenta_empresa: ''
            }
        },
        computed: {
            tipos() {
                return this.$store.getters['contabilidad/tipo-cuenta-empresa/tipos'];
            },
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            init() {
                $(this.$refs.modal).modal('show');

                this.id_empresa = '';
                this.cuenta = '';
                this.id_tipo_cuenta_empresa = '';

                this.$validator.reset()
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-empresa/store', this.$data)
                    .then((data) => {
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