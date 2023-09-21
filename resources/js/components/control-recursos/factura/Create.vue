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
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="archivo">CFDI:</label>
                            <input type="file" class="form-control" id="file_carga" @change="onFileChange"
                                   row="3"
                                   v-validate="{required: true,  ext: ['xml'], size: 3072}"
                                   name="file_carga"
                                   data-vv-as="Archivo de Factura (CFDI)"
                                   ref="file_carga"
                                   :class="{'is-invalid': errors.has('file_carga')}">
                            <div class="invalid-feedback" v-show="errors.has('file_carga')">{{ errors.first('file_carga') }} (xml)</div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group error-content">
                            <label for="idserie" class="col-form-label">Serie:</label>
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
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="tipo" class="col-form-label">Tipo:</label>
                            <select class="form-control"
                                    data-vv-as="Tipo"
                                    id="tipo"
                                    name="tipo"
                                    :error="errors.has('tipo')"
                                    v-validate="{required: true}"
                                    v-model="idtipodocto">
                                <option value>-- Selecionar --</option>
                                <option value="1">Documento para Solicitud de Pago de Orden de Compra</option>
                                <option value="6">Documento para Solicitud de Pago Recurrente</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="data">
                        <div class="form-group error-content">
                            <label for="folio">Folio:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   disabled="true"
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
                    <div class="col-md-6" v-if="data && data.empresa_bd">
                        <div class="form-group error-content">
                            <h6><b>Empresa:</b></h6>
                            <h6>{{data.empresa_bd.razon_social}}</h6>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="data">
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="fecha">Fecha de Facturación:</label>
                            <datepicker v-model = "data.fecha"
                                        name = "fecha"
                                        :format = "formatoFecha"
                                        disabled="true"
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
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="fecha">Fecha de Vencimiento:</label>
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
                    <div class="col-md-6" v-if="data && data.proveedor_bd">
                        <div class="form-group error-content">
                            <h6><b>Proveedor:</b></h6>
                            <h6>{{data.proveedor_bd.razon_social}}</h6>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="data">
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="concepto">Concepto:</label>
                            <input
                                name="concepto"
                                id="concepto"
                                class="form-control"
                                v-model="data.concepto"
                                v-validate="{required: true}"
                                data-vv-as="Concepto"
                                :class="{'is-invalid': errors.has('concepto')}"
                            />
                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="data">
                    <div class="col-md-10">
                        <div class="form-group error-content float-right"><label for="importe">Importe:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(data.subtotal).formatMoney(2)}} </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group error-content float-right"><label for="iva">IVA: {{data.tasa_iva}} %:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(data.impuesto).formatMoney(2)}} </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group error-content float-right"><label for="retenciones">Retenciones:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(data.retencion).formatMoney(2)}}</div>
                    </div>
                    <div class="col-md-5"></div>

                    <div class="col-md-5">
                        <div class="form-group error-content float-right"> <label for="otros">Otros Impuestos:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(data.otros).formatMoney(2)}} </div>
                    </div>
                    <div class="col-md-5"></div>

                    <div class="col-md-5">
                        <div class="form-group error-content float-right"> <label for="total">Total:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(data.total).formatMoney(2)}} </div>
                    </div>
                    <div class="col-md-5"></div>

                    <div class="col-md-5">
                        <div class="form-group error-content float-right"> <label for="moneda">Moneda:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right">{{data.moneda}}  </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary"  v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" >
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
export default {
    name: "factura-create",
    components: {es,datepicker, vueDropzone: vue2Dropzone},
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
        this.getSeries();
    },
    methods : {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
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
                    this.fechasDeshabilitadas.from= new Date(this.data.fecha);
                }).finally(() => {
                    this.cargando = false
                });
            }
            else{
                swal('¡Error!', 'Debe seleccionar un archivo (XML).', 'error')
            }
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(moment(this.data.vencimiento).format('YYYY/MM/DD') < moment(this.data.fecha).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha de facturación no puede ser posterior a la fecha de vencimiento.', 'error')
                    }else {
                        this.store()
                    }
                }
            });
        },
        store() {
            this.data.idserie = this.idserie;
            this.data.idtipodocto = this.idtipodocto;
            this.data.archivo = this.file_carga;
            this.data.nombre_archivo = this.file_carga_name;
            return this.$store.dispatch('controlRecursos/factura/store', this.$data.data)
                .then(data => {
                    this.salir();
                }).finally( ()=>{
                    this.cargando = false;
                });
        },
        salir()
        {
            this.$router.push({name: 'documento'});
        },
    },
}
</script>

<style scoped>

</style>
