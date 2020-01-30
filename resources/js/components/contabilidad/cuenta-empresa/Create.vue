<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_empresa')" :class="btnclass ? btnclass : 'btn btn-app btn-info float-right'" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>

        <div class="modal fade" ref="createModal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">REGISTRAR CUENTA DE EMPRESA</h5>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4" v-if="!id">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Empresa</label>
                                        <empresa-select
                                                name="id_empresa"
                                                placeholder="-- Empresa --"
                                                id="id_empresa"
                                                data-vv-as="Empresa"
                                                v-validate="{required: true}"
                                                v-model="id_empresa"
                                                :error="errors.has('id_empresa')">
                                        ></empresa-select>
                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>

                                <div :class="id ? 'col-md-6' : 'col-md-4'">
                                    <div class="form-group error-content">
                                        <label for="id_tipo_cuenta_empresa">Tipo de Cuenta</label>
                                        <select
                                                :disabled="!id_empresa"
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
                                <div :class="id ? 'col-md-6' : 'col-md-4'">
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
                            <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
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
        props: ['id', 'btnclass'],
        name: "cuenta-empresa-create",
        components: {EmpresaSelect},
        data() {
            return {
                id_empresa: '',
                cuenta: '',
                id_tipo_cuenta_empresa: '',
                tipos: [],
                cargando: false
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            closeModal() {
                $(this.$refs.createModal).modal('hide');
            },

            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    if (this.id) {
                        this.getTipos()
                    }
                    this.cargando = true;
                    $(this.$refs.createModal).modal('show');

                    this.id_empresa = this.id;
                    this.cuenta = '';
                    this.id_tipo_cuenta_empresa = '';

                    this.$validator.reset()
                    this.cargando = false;
                }
            },

            getTipos() {
                return this.$store.dispatch('contabilidad/tipo-cuenta-empresa/index',{
                    params: {
                        scope: 'disponiblesParaEmpresa:' + this.id_empresa
                    }
                })
                    .then(data => {
                        this.tipos = data.data;
                    })
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-empresa/store', this.$data)
                    .then(data => {
                        $(this.$refs.createModal).modal('hide');
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
        watch: {
            id_empresa(value){
                this.tipos = []
                if(value){
                    this.getTipos();
                }
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
