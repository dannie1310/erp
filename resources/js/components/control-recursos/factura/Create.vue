<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group row error-content">
                            <label for="idserie" class="col-md-3 col-form-label">Serie:</label>
                            <div class="col-md-9">
                                <select class="form-control"
                                        data-vv-as="Serie"
                                        id="idserie"
                                        name="idserie"
                                        :error="errors.has('idserie')"
                                        v-validate="{required: true}"
                                        v-model="idserie">
                                    <option value>-- Selecionar --</option>
                                    <option v-for="(serie) in series" :value="serie.id">{{ serie.descripcion }}</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('idserie')">{{ errors.first('idserie') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row error-content">
                            <label for="tipo" class="col-md-3 col-form-label">Tipo Factura:</label>
                            <div class="col-md-9">
                                <select class="form-control"
                                        data-vv-as="Tipo"
                                        id="tipo"
                                        name="tipo"
                                        :error="errors.has('tipo')"
                                        v-validate="{required: true}"
                                        v-model="idtipodocto">
                                    <option value>-- Selecionar --</option>
                                    <option value="1">Factura</option>
                                    <option value="6">Pago Recurrente</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="col-md-6">
                         <div class="col-md-3">
                            <label for="archivo">Archivo de factura:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group error-content">
                                <input type="file" class="form-control" id="file_carga" @change="onFileChange"
                                       row="3"
                                       v-validate="{required: true,  ext: ['xml'], size: 3072}"
                                       name="file_carga"
                                       data-vv-as="Archivo de Factura"
                                       ref="file_carga"
                                       :class="{'is-invalid': errors.has('file_carga')}">
                                <div class="invalid-feedback" v-show="errors.has('file_carga')">{{ errors.first('file_carga') }} (xml)</div>
                            </div>
                        </div>
                    </div>
                    <br />
                </div>
                <div class="row" v-if="data">
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="fecha">Fecha:</label>
                            <datepicker v-model = "data.fecha"
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
                <div class="row" v-if="data">
                    <div class="col-md-12">
                        <div class="form-group row error-content">
                            <div class="form-group error-content">
                                <label for="proveedor">Proveedor:</label>
                                <model-list-select
                                    name="id_proveedor"
                                    placeholder="Seleccionar o buscar por RFC y razón social"
                                    data-vv-as="Proveedor"
                                    v-validate="{required: true}"
                                    v-model="data.id_proveedor"
                                    option-value="id"
                                    :custom-text="rfcAndRazonSocial"
                                    :list="proveedores"
                                    :isError="errors.has(`id_proveedor`)">
                                </model-list-select>
                            </div>
                            <label for="proveedor" class="col-md-3 col-form-label">Proveedor:</label>
                            <label for="proveedor" class="col-md-3 col-form-label">{{data.proveedor_bd.razon_social}} ( {{data.proveedor_bd.rfc}})</label>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="data">
                    <div class="col-md-10">
                        <div class="form-group error-content">
                            <label for="fecha">Empresa:</label>
                            <model-list-select
                                name="id_empresa"
                                placeholder="Seleccionar o buscar por RFC y razón social"
                                data-vv-as="Empresa"
                                v-validate="{required: true}"
                                v-model="data.id_empresa"
                                option-value="id"
                                :custom-text="rfcAndRazonSocial"
                                :list="empresas"
                                :isError="errors.has(`id_empresa`)">
                            </model-list-select>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="data">
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="folio">Folio:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   placeholder="Folio"
                                   name="folio"
                                   id="folio"
                                   data-vv-as="Folio"
                                   v-validate="{required: true}"
                                   v-model="data.folio"
                                   :class="{'is-invalid': errors.has('folio')}">
                            <div class="invalid-feedback" v-show="errors.has('folio')">{{ errors.first('folio') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="fecha">Emisión:</label>
                            <datepicker v-model = "data.emision"
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
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="fecha">Vencimiento:</label>
                            <datepicker v-model = "data.vencimiento"
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
                </div>
                <div class="row" v-if="data">
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
                                   v-model="data.total"
                                   :class="{'is-invalid': errors.has('total')}"
                            >
                            <div class="invalid-feedback" v-show="errors.has('total')">{{ errors.first('total') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="total">Moneda:</label>
                            <select
                                class="form-control"
                                name="id_moneda"
                                data-vv-as="Moneda"
                                v-validate="{required: true}"
                                v-model="data.id_moneda">
                                <option value>-- Selecionar --</option>
                                <option  v-for="(moneda, i) in data.monedas" :value="moneda.id_moneda" >
                                    {{ moneda.moneda }}
                                </option>
                            </select>
                            <div class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row" v-if="data">
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="observaciones">Observaciones:</label>
                            <textarea
                                name="observaciones"
                                id="observaciones"
                                class="form-control"
                                v-model="data.observaciones"
                                v-validate="{required: true}"
                                data-vv-as="Observaciones"
                                :class="{'is-invalid': errors.has('observaciones')}"
                            ></textarea>
                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
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
    components: {es,datepicker, vueDropzone: vue2Dropzone, ModelListSelect},
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
            es: es,
            cargando: false,
            series: [],
            empresas: [],
            proveedores: [],
            idserie: '',
            idtipodocto: '',
            file_carga : null,
            file_carga_name : null,
            data: null,
            fechasDeshabilitadas:{},
        }
    },
    mounted() {
        this.$validator.reset()
        this.file_carga = null
        this.file_carga_name = null
        this.data = null
        this.getEmpresas();
        this.getProveedores();
        this.getSeries();
    },
    methods : {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        rfcAndRazonSocial (item){
            return `[${item.rfc}] - ${item.razon_social}`
        },
        rfcAndRazonSocialProveedor(item)
        {
            return `[${item.rfc}] - ${item.razon_social}`
        },
        getSeries() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/serie/index', {
                params: {sort: 'descripcion', order: 'asc'}
            })
            .then(data => {
                this.series = data.data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        getEmpresas() {
            return this.$store.dispatch('controlRecursos/empresa/index', {
                params: {sort: 'RazonSocial', order: 'asc'}
            })
                .then(data => {
                    this.empresas = data.data;
                })
        },
        getProveedores() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: {sort: 'RazonSocial', order: 'asc', scope:'porRFC'}
            }).then(data => {
                this.proveedores = data.data;
            })
        },
        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.file_carga = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        onFileChange(e){
            this.file_carga = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.file_carga_name = files[0].name;
            this.createImage(files[0]);
            if(files[0].type == "text/xml")
            {
                setTimeout(() => {
                    this.getLayoutData()
                }, 500);
            } else {
                swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
            }
        },
        getLayoutData(){
            if(this.file_carga != null && this.file_carga_name !='') {
                var formData = new FormData();
                formData.append('factura', this.file_carga);
                formData.append('nombre_archivo', this.file_carga_name);
                return this.$store.dispatch('controlRecursos/factura/cargaCFDI', {
                    data: formData, config: {params: {_method: 'POST'}}
                }).then(data => {
                    this.data = data
                    this.data.emision = new Date(this.data.fecha);
                    this.data.vencimiento = new Date(this.data.fecha);
                    this.fechasDeshabilitadas.from= new Date(this.data.fecha);
                }).finally(() => {
                    this.cargando = false
                });
            }
            else{
                swal('¡Error!', 'Debe seleccionar un archivo (XML).', 'error')
            }
        },
    },
}
</script>

<style scoped>

</style>
