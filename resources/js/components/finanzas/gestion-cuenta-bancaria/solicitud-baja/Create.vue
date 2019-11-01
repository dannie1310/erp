<template>
     <span>
        <button @click="init" v-if="$root.can('solicitar_baja_cuenta_bancaria_empresa')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Solicitud
        </button>
          <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR SOLICITUD DE BAJA DE CUENTA BANCARIA</h5>
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
                                        <label for="cuenta" class="col-sm-2 col-form-label">Cuenta / Clabe: </label>
                                        <div class="col-sm-10">
                                            <select
                                                    :disabled="!bandera_empresa"
                                                    type="text"
                                                    name="cuenta"
                                                    data-vv-as="Cuenta"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="cuenta"
                                                    v-model="cuenta"
                                                    :class="{'is-invalid': errors.has('cuenta')}"
                                            >
                                                <option value>-- Seleccione una Cuenta --</option>
                                                <option v-for="cuenta in cuentas" :value="cuenta">{{ cuenta.cuenta }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('cuenta') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row" v-if="detalle == 1">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Datos de la Cuenta</h5>
                                            </div>
                                        </div>
                                        <form role="form">
                                            <div class="row">
                                                <div class="table-responsive col-md-12">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                            <tr>
                                                                <td><b>Banco:</b></td>
                                                                <td>{{cuenta.banco.razon_social}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Moneda:</b></td>
                                                                <td>{{cuenta.moneda.nombre}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Sucursal:</b></td>
                                                                <td>{{cuenta.sucursal}}</td>
                                                            </tr>
                                                             <tr>
                                                                <td><b>Plaza:</b></td>
                                                                <td>{{cuenta.plaza.clave_format}}</td>
                                                             </tr>
                                                            <tr>
                                                                <td><b>Tipo:</b></td>
                                                                <td>{{cuenta.tipo}}</td>
                                                             </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </form>
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
                             <div class="row">
                                 <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="archivo" class="col-sm-2 col-form-label">Cargar Archivo de Soporte: </label>
                                        <div class="col-sm-10">
                                            <input type="file" class="form-control" id="archivo" @change="onFileChange"
                                                   row="3"
                                                   v-validate="{required: true,  ext: ['pdf'], size: 3072}"
                                                   name="archivo"
                                                   data-vv-as="Archivo"
                                                   ref="archivo"
                                                   :class="{'is-invalid': errors.has('archivo')}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has('archivo')">{{ errors.first('archivo') }} (pdf)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 || id_empresa =='' || id_cuenta ==''">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
          </div>
     </span>
</template>

<script>
    export default {
        name: "solicitud-baja-create",
        data() {
            return {
                cargando: false,
                id_empresa: '',
                bandera_empresa: 0,
                empresas: [],
                id_cuenta: '',
                cuenta: '',
                cuentas: [],
                observaciones: '',
                archivo: null,
                detalle: 0
            }
        },
        mounted(){
            this.getBeneficiario();
        },
        computed: {

        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
                this.id_empresa = '';
                this.bandera_empresa = 0;
                this.cuenta = '';
                this.id_cuenta = '';
                this.$validator.reset();
                this.cargando = false;
                this.observaciones = '';
                this.archivo = null;
                this.$refs.archivo.value = '';
                this.detalle = 0;
            },
            getBeneficiario(){
                this.empresas = [];
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        scope: 'beneficiarioCuentaBancaria',  sort: 'razon_social', order: 'asc'
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
            },
            getCuentas(){
                this.detalle = 0;
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id_empresa,
                    params: {include: ['cuentas_bancarias', 'cuentas_bancarias.moneda', 'cuentas_bancarias.plaza', 'cuentas_bancarias.banco']}
                }).then(data => {
                    this.cuentas = data.cuentas_bancarias.data;
                    this.bandera_empresa = 1;
                })
            },
            store() {
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/store', this.$data)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.archivo == null){
                            swal('¡Error!', 'Error al cargar el archivo, favor de seleccionarlo nuevamente.', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
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
            id_empresa(value){
                if(value != ''){
                    this.getCuentas();
                }
            },
            cuenta(value){
                if(value != ''){
                    this.id_cuenta = value.id;
                    this.detalle = 1;
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