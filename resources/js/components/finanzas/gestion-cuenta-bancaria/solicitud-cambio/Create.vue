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
                bandera_empresa: 0,
                empresas: [],
                cuenta: '',
                cuentas: [],
                observaciones: '',
                archivo: null
            }
        },
        mounted(){
            this.getBeneficiario();
            this.getCuentas();
        },
        computed: {

        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
                this.id_empresa = '';
                this.empresas = [];
                this.bandera_empresa = 0;
                this.cuenta = '';
                this.$validator.reset();
                this.cargando = false;
                this.observaciones = '';
                this.archivo = null;
                this.$refs.archivo.value = '';
            },
            getBeneficiario(){
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        sort: 'razon_social', order: 'asc',
                        scope: 'beneficiarioCuentaBancaria'
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.bandera_empresa = 1;
                    })
            },
            getCuentas(){

            },
            store() {
            },
            validate() {

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