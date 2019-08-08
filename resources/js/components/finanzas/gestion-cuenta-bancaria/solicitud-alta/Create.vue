<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Solicitud
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR SOLICITUD DE ALTA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group error-content">
                                            <div class="form-group">
                                                <div class="btn-group btn-group-toggle">
                                                    <label class="btn btn-outline-secondary" :class="id_tipo_empresa === Number(key) ? 'active': ''" v-for="(tipo_empresa, key) in tipos_empresas" :key="key">
                                                        <input type="radio"
                                                               class="btn-group-toggle"
                                                               name="id_tipo_empresa"
                                                               :id="'tipo_empresa' + key"
                                                               :value="key"
                                                               autocomplete="on"
                                                               v-model.number="id_tipo_empresa">
                                                        {{ tipo_empresa }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                         <div class="form-group">
                                        <label for="id_empresa">Beneficiario: </label>
                                        <select
                                                :disabled="!bandera_empresa"
                                                type="text"
                                                name="id_empresa"
                                                data-vv-as="Beneficiario"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_empresa"
                                                v-model="id_empresa"
                                                :class="{'is-invalid': errors.has('id_empresa')}"
                                        >
                                            <option value>-- Seleccione Transacción --</option>
                                            <option v-for="empresa in empresas" :value="empresa.id_empresa">{{ empresa.razon_social }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                         <div class="form-group">
                                        <label for="id_banco">Banco: </label>
                                        <select
                                                type="text"
                                                name="id_banco"
                                                data-vv-as="Banco"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_banco"
                                                v-model="id_banco"
                                                :class="{'is-invalid': errors.has('id_banco')}"
                                        >
                                            <option value>-- Seleccione Transacción --</option>
                                            <option v-for="banco in bancos" :value="banco.id_empresa">{{ banco.razon_social }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_banco')">{{ errors.first('id_banco') }}</div>
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "solicitud-alta-create",
        data() {
            return {
                cargando: false,
                id_tipo_empresa: '',
                tipos_empresas: {
                    1: "Proveedor / Contratista",
                    2: "Responsable de Fondo Fijo"
                },
                id_empresa: '',
                empresas: [],
                bandera_empresa: 0,
                id_banco: '',
                bancos: []
            }
        },
        mounted(){
          this.getBancos();
        },
        computed: {

        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
            },
            getBancos(){
                return this.$store.dispatch('cadeco/banco/index', {
                    params: {
                        scope: 'bancoGlobal'
                    }
                })
                    .then(data => {
                        this.bancos = data;
                    })
            },
            getEmpresa(){
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        scope: 'proveedorContratista'
                    }
                })
                    .then(data => {
                        this.empresa = data;
                        this.bandera_empresa = 1;
                    })
            },
            getFondoFijo(){
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        scope: 'responsableFondoFijo'
                    }
                })
                    .then(data => {
                        this.empresa = data;
                        this.bandera_empresa = 1;
                    })
            }
        },
        watch: {
            id_tipo_empresa(value){
                if(value != ''){
                    if(value == 1){
                        this.getEmpresa();
                    }
                    if(value == 2){
                        this.getFondoFijo();
                    }
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