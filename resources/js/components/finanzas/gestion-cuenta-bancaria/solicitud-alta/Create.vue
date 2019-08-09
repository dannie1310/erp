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
                                <div class="form-group row error-content">
                                    <div class="col-sm-12">
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_empresa" class="col-sm-2 col-form-label">Beneficiario: </label>
                                        <div class="col-sm-10">
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
                                                    <option value>-- Seleccione un beneficiario --</option>
                                                    <option v-for="empresa in empresas" :value="empresa.id">{{ empresa.razon_social }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_banco" class="col-sm-2 col-form-label">Banco: </label>
                                        <div class="col-sm-10">
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
                                                <option value>-- Seleccione un Banco --</option>
                                                <option v-for="banco in bancos" :value="banco.id">{{ banco.razon_social }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('id_banco')">{{ errors.first('id_banco') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group row error-content">
                                    <label for="id_tipo" class="col-sm-2 col-form-label">Tipo: </label>
                                    <div class="col-sm-10">
                                        <div class="btn-group btn-group-toggle">
                                            <label class="btn btn-outline-secondary" :class="id_tipo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos" :key="llave">
                                                <input type="radio"
                                                               class="btn-group-toggle"
                                                               name="id_tipo"
                                                               :id="'tipo' + llave"
                                                               :value="llave"
                                                               autocomplete="on"
                                                               v-model.number="id_tipo">
                                                        {{ tipo}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="tamano_cuenta">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="cuenta" class="col-sm-2 col-form-label">Cuenta:</label>
                                        <div class="col-sm-10">
                                            <input
                                                    :disabled="!id_tipo"
                                                    type="number"
                                                    name="cuenta"
                                                    data-vv-as="Cuenta"
                                                    v-validate="{required: true, min:9, max:18}"
                                                    class="form-control"
                                                    v-mask="tamano_cuenta"
                                                    id="cuenta"
                                                    placeholder="Cuenta"
                                                    v-model="cuenta"
                                                    :class="{'is-invalid': errors.has('cuenta')}">
                                            <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_moneda" class="col-sm-2 col-form-label">Moneda: </label>
                                        <div class="col-sm-10">
                                            <select
                                                    type="text"
                                                    name="id_moneda"
                                                    data-vv-as="Moneda"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="id_moneda"
                                                    v-model="id_moneda"
                                                    :class="{'is-invalid': errors.has('id_moneda')}"
                                            >
                                                <option value>-- Seleccione un Moneda --</option>
                                                <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="sucursal" class="col-sm-2 col-form-label">Sucursal: </label>
                                        <div class="col-sm-10">
                                            <input
                                                    type="number"
                                                    step="any"
                                                    name="sucursal"
                                                    data-vv-as="Sucursal"
                                                    v-validate="{required: true, digits: 3}"
                                                    class="form-control"
                                                    id="sucursal"
                                                    placeholder="Sucursal"
                                                    v-model="sucursal"
                                                    :class="{'is-invalid': errors.has('sucursal')}">
                                            <div class="invalid-feedback" v-show="errors.has('sucursal')">{{ errors.first('sucursal') }}</div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_plaza" class="col-sm-2 col-form-label">Plaza: </label>
                                        <div class="col-sm-10">
                                            <select
                                                    type="text"
                                                    name="id_plaza"
                                                    data-vv-as="Plaza"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="id_plaza"
                                                    v-model="id_plaza"
                                                    :class="{'is-invalid': errors.has('id_plaza')}"
                                            >
                                                <option value>-- Seleccione un Plaza --</option>
                                                <option v-for="plaza in plazas" :value="plaza.id">{{ plaza.clave }} {{plaza.nombre}}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('id_plaza')">{{ errors.first('id_plaza') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="observaciones" class="col-sm-2 col-form-label">Observaciones: </label>
                                        <div class="col-sm-10">
                                            <textarea
                                                    name="observaciones"
                                                    id="observaciones"
                                                    class="form-control"
                                                    v-model="observaciones"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Observaciones"
                                                    :class="{'is-invalid': errors.has('observaciones')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 || cuenta =='' ">Registrar</button>
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
                bancos: [],
                cuenta: '',
                id_tipo: '',
                tipos: {
                    1: "Interbancaria",
                    2: "Mismo Banco"
                },
                tamano_cuenta: '###-###-#########-#',
                id_moneda: '',
                monedas: [],
                id_plaza: '',
                plazas: [],
                sucursal: '',
                observaciones: ''
            }
        },
        mounted(){
          this.getBancos();
          this.getMonedas();
          this.getPlazas();
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
            getMonedas(){
                return this.$store.dispatch('cadeco/moneda/index', {

                })
                    .then(data => {
                        this.monedas = data.data;
                    })
            },
            getPlazas(){
            //    aqui plazas
            },
            getEmpresa(){
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        scope: 'proveedorContratista'
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
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
            },
            store() {
                return this.$store.dispatch('finanzas/solicitud-alta-cuenta-bancaria/store', this.$data)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
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
            id_tipo_empresa(value){
                if(value != ''){
                    if(value == 1){
                        this.getEmpresa();
                    }
                    if(value == 2){
                        this.getFondoFijo();
                    }
                }
            },
            id_tipo(value){
                if(value != ''){
                    this.cuenta = '';
                    if(value == 1){
                        this.tamano_cuenta = '###-###-#########-#'
                    }
                    if(value == 2){
                        this.tamano_cuenta = '####-####-#'
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