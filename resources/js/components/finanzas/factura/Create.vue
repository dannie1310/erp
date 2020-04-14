<template>
    <span>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_factura')" :disabled="cargando">
            <button @click="init" class="btn btn-app btn-info float-right">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
            </button>
        </div>
    </div>
    <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_factura"> <i class="fas fa-file-invoice-dollar"></i> REGISTRAR FACTURA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cleanData">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form role="form" @submit.prevent="validate">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input button" id="es_nacional" v-model="dato.es_nacional" >
                                <label class="custom-control-label" for="es_nacional">Proveedor Nacional</label>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-switch" v-if="dato.es_nacional">
                                <input type="checkbox" class="custom-control-input button" id="es_deducible" v-model="dato.es_deducible" >
                                <label class="custom-control-label" for="es_deducible">Es Deducible</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-switch" v-if="dato.es_nacional && dato.es_deducible">
                                <input type="checkbox" class="custom-control-input button" id="con_nota_credito" v-model="dato.con_nota_credito" >
                                <label class="custom-control-label" for="con_nota_credito">Aplicar Nota de Crédito</label>
                            </div>
                        </div>
                    </div>
                    <hr v-if="dato.es_deducible && dato.es_nacional" />
                    <div class="row" v-if="dato.es_deducible && dato.es_nacional">
                        <div class="col-md-3">
                            <label for="archivo">Archivo de factura:</label>
                        </div>

                        <div class="col-md-9">
                            <div class="form-group error-content" >

                                <input type="file" class="form-control" id="archivo" @change="onFileChange"
                                       row="3"
                                       v-validate="{required: true,  ext: ['xml'], size: 3072}"
                                       name="archivo"
                                       data-vv-as="Archivo de Factura"
                                       ref="archivo"
                                       :class="{'is-invalid': errors.has('archivo')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('archivo')">{{ errors.first('archivo') }} (xml)</div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="dato.es_deducible && dato.es_nacional && dato.con_nota_credito">
                        <div class="col-md-3">
                            <label for="archivo">Archivo de nota de crédito:</label>
                        </div>

                        <div class="col-md-9">
                            <div class="form-group error-content" >

                                <input type="file" class="form-control" id="archivo_nc" @change="onFileChangeNC"
                                       row="3"
                                       v-validate="{required: true,  ext: ['xml'], size: 3072}"
                                       name="archivo_nc"
                                       data-vv-as="Archivo Nota de Crédito"
                                       ref="archivo_nc"
                                       :class="{'is-invalid': errors.has('archivo_nc')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('archivo_nc')">{{ errors.first('archivo_nc') }} (xml)</div>
                            </div>
                        </div>
                    </div>
                    <hr />

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha">Fecha:</label>
                                    <datepicker v-model = "dato.fecha"
                                                name = "fecha"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                v-validate="{required: true}"
                                                :disabled-dates="fechasDeshabilitadas"
                                                :class="{'is-invalid': errors.has('fecha')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group error-content">
                                <label for="fecha">Empresa:</label>
                                <model-list-select v-if="dato.es_deducible && dato.es_nacional"
                                        name="id_empresa"
                                        placeholder="Seleccionar o buscar por RFC y razón social"
                                        data-vv-as="Empresa"
                                        v-validate="{required: true}"
                                        v-model="dato.id_empresa"
                                        option-value="id"
                                        :custom-text="rfcAndRazonSocial"
                                        :list="empresas"
                                        :isError="errors.has(`id_empresa`)">
                                </model-list-select>
                                <model-list-select v-if="!dato.es_nacional"
                                        v-else
                                        name="id_empresa"
                                        placeholder="Seleccionar o buscar por razón social de proveedor extranjero"
                                        data-vv-as="Empresa"
                                        v-validate="{required: true}"
                                        v-model="dato.id_empresa"
                                        option-value="id"
                                        option-text="razon_social"
                                        :list="empresas_extranjeras"
                                        :isError="errors.has(`id_empresa`)">
                                </model-list-select>
                                <model-list-select v-if="!dato.es_deducible && dato.es_nacional"
                                        name="id_empresa"
                                        placeholder="Seleccionar o buscar por razón social de proveedor no deducible"
                                        data-vv-as="Empresa"
                                        v-validate="{required: true}"
                                        v-model="dato.id_empresa"
                                        option-value="id"
                                        option-text="razon_social"
                                        :list="empresas_no_deducibles"
                                        :isError="errors.has(`id_empresa`)">
                                </model-list-select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <!--Referencia-->
                                <div class="form-group error-content">
                                    <label for="referencia">Folio:</label>
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Referencia"
                                           name="referencia"
                                           id="referencia"
                                           data-vv-as="Referencia"
                                           v-validate="{required: true}"
                                           v-model="dato.referencia"
                                           :class="{'is-invalid': errors.has('referencia')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <!--Rubro-->
                            <div class="form-group error-content">
                                <label for="referencia">Rubro de Factura:</label>
                                <model-list-select
                                        name="id_rubro"
                                        placeholder="Seleccionar o buscar"
                                        data-vv-as="Rubro"
                                        v-validate="{required: true}"
                                        v-model="dato.id_rubro"
                                        option-value="id"
                                        option-text="descripcion"
                                        :list="rubros"
                                        :isError="errors.has('id_rubro')">
                                </model-list-select>
                                <div class="invalid-feedback" v-show="errors.has('id_rubro')">{{ errors.first('id_rubro') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <label for="fecha">Emisión:</label>
                                <datepicker v-model = "dato.emision"
                                            name = "emision"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('emision')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('emision')">{{ errors.first('emision') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <label for="fecha">Vencimiento:</label>
                                <datepicker v-model = "dato.vencimiento"
                                            name = "vencimiento"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('vencimiento')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('vencimiento')">{{ errors.first('vencimiento') }}</div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <label for="total">Total:</label>
                                <input class="form-control"
                                       style="width: 100%"
                                       placeholder="Total de Factura"
                                       name="total"
                                       id="total"
                                       data-vv-as="Total"
                                       v-validate="{required: true, decimal:2}"
                                       v-model="dato.total"
                                       :class="{'is-invalid': errors.has('total')}"
                                >

                                <div class="invalid-feedback" v-show="errors.has('total')">{{ errors.first('total') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <label for="total">Moneda:</label>
                                <select
                                        class="form-control"
                                        name="id_moneda"
                                        data-vv-as="Moneda"
                                        v-validate="{required: true}"
                                        v-model="dato.id_moneda"
                                        >
                                    <option  v-for="(moneda, i) in monedas" :value="moneda.id" >
                                        {{ moneda.nombre }}
                                    </option>
                                </select>

                                <div class="invalid-feedback" v-show="errors.has('moneda')">{{ errors.first('moneda') }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label for="observaciones">Observaciones:</label>
                                <textarea
                                        name="observaciones"
                                        id="observaciones"
                                        class="form-control"
                                        v-model="dato.observaciones"
                                        v-validate="{required: true}"
                                        data-vv-as="Observaciones"
                                        :class="{'is-invalid': errors.has('observaciones')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cleanData">Cerrar</button>
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 ">Registrar</button>
                </div>
                    </form>
            </div>
        </div>
    </div>
    </span>

</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import vue2Dropzone from 'vue2-dropzone';
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "factura-create",
        components: {datepicker,ModelListSelect, vueDropzone: vue2Dropzone},
        data() {
            return {
                dropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 150,
                    maxFilesize: 0.5,
                    headers: { "My-Awesome-Header": "header value" },
                    addRemoveLinks: true,
                    dictDefaultMessage: "<h2 style='color:#bbb'><span class='fas fa-file-invoice-dollar' style='padding-right:5px'></span>Arrastra y suelta el archivo xml a esta zona.</h2>",
                },
                cargando:true,
                es:es,
                fechasDeshabilitadas:{},
                empresas:[],
                empresas_no_deducibles:[],
                rubros:[],
                monedas:[],
                dato:{
                    es_deducible:1,
                    es_nacional:1,
                    con_nota_credito:0,
                    fecha:'',
                    emision:'',
                    vencimiento:'',
                    id_empresa:'',
                    id_moneda:'',
                    id_rubro:'',
                    referencia:'',
                    total:0,
                    observaciones:'',
                    archivo:null,
                    archivo_name:null,
                    archivo_nc:null,
                    archivo_nc_name:null,
                },
            }
        },

        mounted() {

            this.getMonedas();
            this.dato.id_moneda =1;
            this.dato.fecha = new Date();
            this.dato.emision = new Date();
            this.dato.vencimiento = new Date();
            this.fechasDeshabilitadas.from= new Date();
        },
        methods:{
            cleanData(){
                this.dato.id_moneda =1;
                this.dato.fecha = new Date();
                this.dato.emision = new Date();
                this.dato.vencimiento = new Date();
                this.dato.referencia = '';
                this.dato.observaciones = '';
                this.dato.archivo = '';
                this.dato.es_deducible = 1;
                this.dato.es_nacional = 1;
                this.con_nota_credito = 0;
                this.dato.total = 0;
                this.dato.id_empresa = '';
                this.dato.id_rubro = '';
                this.$refs.archivo.value='';
                this.$refs.archivo_nc.value='';
            },
            init() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$validator.reset()
            },
            rfcAndRazonSocial (item){
                return `[${item.rfc}] - ${item.razon_social}`
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getEmpresas() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Deducibles' }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
                    .finally(()=>{
                        this.getRubros();
                    })
            },
            getRubros() {
                return this.$store.dispatch('finanzas/rubro/index', {
                    params: {sort: 'descripcion', order: 'asc', scope:'paraFactura' }
                })
                    .then(data => {
                        this.rubros = data.data;
                    })
                    .finally(()=>{
                        this.getEmpresasNoDeducibles();
                    })
            },
            getEmpresasNoDeducibles() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'noDeducibles' }
                })
                    .then(data => {
                        this.empresas_no_deducibles = data.data;
                    })
                    .finally(()=>{
                        this.getEmpresasExtranjeras();
                    })

            },
            getEmpresasExtranjeras() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'extranjeras' }
                })
                    .then(data => {
                        this.empresas_extranjeras = data.data;
                        this.cargando = false;
                    })

            },
            getMonedas() {
                this.cargando =true;
                return this.$store.dispatch('cadeco/moneda/index', {
                    params: {sort: 'nombre', order: 'desc' }
                })
                    .then(data => {
                        this.monedas = data.data;
                    })
                    .finally(()=>{
                        this.getEmpresas();
                    })
            },
            onFileChange(e){
                this.dato.archivo = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.dato.archivo_name = files[0].name;
                this.createImage(files[0], 1);

                if(files[0].type == "text/xml")
                {
                    setTimeout(() => {
                        this.cargarXML(1)
                    }, 500);
                } else {
                    swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
                }

            },

            onFileChangeNC(e){
                this.dato.archivo_nc = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.dato.archivo_nc_name = files[0].name;
                this.createImageNC(files[0], 1);

                if(files[0].type == "text/xml")
                {
                    setTimeout(() => {
                        this.cargarXML(2)
                    }, 500);
                } else {
                    swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
                }

            },

            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.dato.archivo = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            createImageNC(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.dato.archivo_nc = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            cargarXML(tipo){
                this.cargando = true;
                var formData = new FormData();
                formData.append('tipo',  tipo);
                formData.append('id_empresa',  this.dato.id_empresa);
                if(tipo == 1){
                    formData.append('xml',  this.dato.archivo);
                    formData.append('nombre_archivo',  this.dato.archivo_name);
                } else if(tipo == 2){
                    formData.append('xml',  this.dato.archivo_nc);
                    formData.append('nombre_archivo',  this.dato.archivo_name_nc);
                }

                return this.$store.dispatch('finanzas/factura/cargarXML',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        var count = Object.keys(data).length;

                        if(count > 0 ){
                            if(data.tipo_comprobante === "I"){

                                this.dato.total = (parseFloat(this.dato.total) + parseFloat(data.total)).toFixed(2);
                                this.dato.referencia = data.serie + data.folio;
                                this.dato.emision = data.fecha;
                                this.dato.id_empresa = data.empresa_bd.id_empresa;
                                this.dato.id_moneda = data.moneda_bd.id_moneda;
                                this.empresas.push({id:data.empresa_bd.id_empresa,razon_social:data.empresa_bd.razon_social,rfc:data.empresa_bd.rfc});
                            } else if(data.tipo_comprobante === "E"){
                                this.dato.total = (parseFloat(this.dato.total) - parseFloat(data.total)).toFixed(2);
                            }


                        }else{
                            if(this.$refs.archivo.value !== ''){
                                this.$refs.archivo.value = '';
                                this.dato.archivo = null;
                            }
                            this.cleanData();
                            swal('Carga con XML', 'Archivo sin datos válidos', 'warning')
                        }
                    }).finally(() => {
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            validate_xml() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('finanzas/factura/store', this.$data.dato)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                        this.cleanData();
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
        }
    }
</script>
<style>
    .dropzone-custom-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .dropzone-custom-title {
        margin-top: 0;
        color: #999;
    }

    .subtitle {
        color: #7ac142;
    }
    .vue-dropzone {
        border: 2px dashed #e5e5e5;
    }
</style>
