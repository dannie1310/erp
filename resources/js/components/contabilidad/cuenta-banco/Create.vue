<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_contable_bancaria')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Cuenta de Banco
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE BANCO:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Empresa</label>
                                        <empresa-autocomplete
                                                name="id_empresa"
                                                id="id_empresa"
                                                data-vv-as="Empresa"
                                                v-validate="{required: true}"
                                                v-model="id_empresa"
                                                v-bind:scope="'paraBancos'"
                                                :class="{'is-invalid': errors.has('id_empresa')}">
                                        ></empresa-autocomplete>
                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_cuenta">Cuenta</label>
                                         <select
                                                 type="text"
                                                 name="id_cuenta"
                                                 data-vv-as="Cuenta"
                                                 v-validate="{required: true}"
                                                 class="form-control"
                                                 id="id_cuenta"
                                                 v-model="id_cuenta"
                                                 :class="{'is-invalid': errors.has('id_cuenta')}"
                                         >
                                            <option value>-- Cuenta --</option>
                                            <option v-for="c in cuentas" :value="c.id">{{ c.numero }} ({{c.abreviatura}})</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_tipo_cuenta_contable">Tipo de Cuenta</label>
                                        <select
                                                type="text"
                                                name="id_tipo_cuenta_contable"
                                                data-vv-as="Tipo de Cuenta Contable"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_tipo_cuenta_contable"
                                                v-model="id_tipo_cuenta_contable"
                                                :class="{'is-invalid': errors.has('id_tipo_cuenta_contable')}"
                                        >
                                            <option value>-- Tipo de Cuenta --</option>
                                            <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_cuenta_contable')">{{ errors.first('id_tipo_cuenta_contable') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cuenta">Cuenta Contable</label>
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
    import EmpresaAutocomplete from "../../cadeco/empresa/Autocomplete";
    export default {
        name: "cuenta-banco-create",
        components: {EmpresaAutocomplete},
        data() {
            return {
                id_cuenta: '',
                id_empresa: '',
                cuenta: '',
                id_tipo_cuenta_contable: '',
                cuentas: [],
                tipos: []
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.id_cuenta = '';
                this.cuenta = '';
                this.id_tipo_cuenta_contable = '';
                this.id_empresa = '';

                this.$validator.reset()
            },
            getCuentas(){
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id_empresa,
                    params: { include: 'cuentas' }
                })
                    .then(data => {
                        this.cuentas = data.cuentas.data;
                    })
            },
            getTipos() {
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/tipos',{
                    scope: 'paraDisponibles'
                });
            },
            store() {
                return this.$store.dispatch('contabilidad/cuenta-banco/store', this.$data)
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
        },
        watch: {
            id_empresa(value){
                this.cuentas = []
                if(value){
                    this.getCuentas();
                }
            },
            id_cuenta(value){
                this.tipos = [];
                if(value){
                    this.getTipos();
                }
            }
        }
    }
</script>