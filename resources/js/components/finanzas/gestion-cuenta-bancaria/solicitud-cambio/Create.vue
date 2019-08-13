<template>
     <span>
        <button @click="init" v-if="$root.can('solicitar_cambio_cuenta_bancaria_empresa')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Solicitud
        </button>
          <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR SOLICITUD DE CAMBIO DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_empresa" class="col-sm-2 col-form-label">Beneficiario: </label>
                                        <div class="col-sm-10">
                                            <select
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
                                        <label for="id_cuenta" class="col-sm-2 col-form-label">Cuenta / Clabe: </label>
                                        <div class="col-sm-10">
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
                                                <option value>-- Seleccione un Banco --</option>
                                                <option v-for="cuenta in cuentas" :value="cuenta.id">{{ cuenta }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
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
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 || cuenta =='' || id_tipo_empresa =='' || id_empresa == ''">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
          </div>
     </span>
</template>

<script>
    export default {
        name: "solicitud-cambio-create",
        data() {
            return {
                cargando: false,
                id_empresa: '',
                empresas: [],
                cuenta: '',
                id_tipo: '',
                tipos: {
                    1: "Interbancaria",
                    2: "Mismo Banco"
                },
                id_moneda: '',
                monedas: [],
                id_plaza: '',
                plaza: '',
                plazas: [],
                plaza_clave: '',
                sucursal: '',
                observaciones: '',
                archivo: null
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
                this.id_tipo_empresa = '';
                this.id_empresa = '';
                this.empresas = [];
                this.bandera_empresa = 0;
                this.id_banco = '';
                this.banco_clave = '';
                this.cuenta = '';
                this.id_tipo = '';
                this.id_moneda = '';
                this.id_plaza = '';
                this.plaza = '';
                this.plaza_clave = '';
                this.sucursal = '';
                this.$validator.reset();
                this.cargando = false;
                this.observaciones = '';
                this.archivo = null;
                this.$refs.archivo.value = '';
            },
            getBancos(){
                this.bancos = [];
                return this.$store.dispatch('cadeco/banco/index', {
                    params: {
                        include: 'ctg_banco',
                        scope: 'bancoGlobal'
                    }
                })
                    .then(data => {
                        this.bancos = data.data;
                    })
            },
            getMonedas(){
                this.monedas = [];
                return this.$store.dispatch('cadeco/moneda/index', {

                })
                    .then(data => {
                        this.monedas = data.data;
                    })
            },
            getPlazas(){
                this.plazas = [];
                return this.$store.dispatch('seguridad/finanzas/ctg-plaza/index', {
                    params: {
                        sort: 'clave', order: 'asc'
                    }
                })
                    .then(data => {
                        this.plazas = data.data;
                    })
            },
            getEmpresa(){
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        sort: 'razon_social', order: 'asc',
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
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.getPlaza();
                this.$validator.validate().then(result => {
                    if (result) {
                        if (this.id_tipo == 1 && this.cuenta.length < 18) {
                            swal('¡Error!', 'La cuenta tipo interbancaria debe contar con 18 digitos.', 'error')
                        }
                        else if (this.id_tipo == 2 && this.cuenta.length > 9) {
                            swal('¡Error!', 'La cuenta de mismo banco debe contar con 9 digitos.', 'error')
                        }
                        else if (this.id_tipo == 1 && this.cuenta.length == 18 && this.cuenta.substring(0, 3) != this.banco_clave) {
                            swal('¡Error!', 'La cuenta no corresponde con la clave del banco.', 'error');
                        }
                        else if (this.id_tipo == 1 && this.cuenta.length == 18 && this.cuenta.substring(3, 6) != this.plaza_clave) {
                            swal('¡Error!', 'La cuenta no corresponde con la clave de la plaza.', 'error')
                        }else if(this.archivo == null){
                            swal('¡Error!', 'Error al cargar el archivo, favor de seleccionarlo nuevamente.', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
            },
            getClave(banco){
                this.banco_clave = banco.ctg_banco.clave_format;
                return this.banco_clave;
            },
            getPlaza(){
                this.plaza_clave = this.plaza.clave_format;
                this.id_plaza = this.plaza.id;
            },
            onFileChange(e){
                this.archivo = null;
                var files = e.target.files || e.dataTransfer.files;
                this.createImage(files[0], 1);
                setTimeout(() => {
                    if(this.archivo == null) {
                        onFileChange(e)
                    }
                }, 500);
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.archivo = e.target.result;
                };
                reader.readAsDataURL(file);
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