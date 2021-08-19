<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-2">
                        <label >Tipo de Transacción:</label>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group error-content" >
                            <model-list-select
                                v-if="tipos_transaccion"
                                name="tipo"
                                id="tipo"
                                placeholder="Seleccionar o buscar"
                                data-vv-as="Tipo de Transacción"
                                v-validate="{required: true}"
                                v-model="id_tipo_transaccion"
                                option-value="id"
                                option-text="descripcion"
                                :list="tipos_transaccion"
                                :isError="errors.has('tipo')"
                            ></model-list-select>
                            <input type="text" class="form-control" value="Cargando..." v-else disabled="disabled" />
                            <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>

                        </div>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-2">
                        <label for="archivo">Archivo del CFDI (XML):</label>
                    </div>
                    <div class="col-md-4">
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
            </div>
        </div>
        <cfdi-show v-if="cargado" v-bind:cfdi="dato"></cfdi-show>
        <div class="pull-right" style="padding-bottom: 48px" >
            <button type="button" class="btn btn-primary" @click="validate()" :disabled="errors.count() > 0 || !cargado"  >
                Continuar <i class="fa fa-angle-right"></i>
            </button>
        </div>

    </span>


</template>

<script>

    import CfdiShow from "../../fiscal/cfd/cfd-sat/Show";
    import {ModelListSelect} from "vue-search-select";
    export default {
        name: "seleccionar-cfdi",
        components: {CfdiShow, ModelListSelect},
        data() {
            return {
                cargando:true,
                cargado:false,
                id_tipo_transaccion:null,
                dato:{
                    id_cfdi : '',
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
            this.getTiposTransaccion();
        },
        methods:{
            getTiposTransaccion(){
                this.$store.commit('entrega-cfdi/tipo-transaccion/SET_TIPOS', null);
                return this.$store.dispatch('entrega-cfdi/tipo-transaccion/index', {
                    params: {include: [], sort: 'orden', order: 'asc'}
                }).then(data => {
                    this.$store.commit('entrega-cfdi/tipo-transaccion/SET_TIPOS', data);
                    this.cargando = false;
                })
            },
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
                this.dato.total_impuestos_retenidos = 0;
                this.dato.total_impuestos_trasladados = 0;
                this.dato.id_empresa = '';
                this.dato.id_rubro = '';
                this.$refs.archivo.value='';
                this.$refs.archivo_nc.value='';
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
                    swal('Cargar XML', 'El archivo debe ser en formato XML', 'error')
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
            cargarXML(tipo){
                this.cargando = true;
                var formData = new FormData();
                formData.append('tipo',  tipo);
                formData.append('id_empresa',  this.dato.id_empresa);
                formData.append("id_tipo_transaccion", this.id_tipo_transaccion);
                formData.append('xml',  this.dato.archivo);
                formData.append('nombre_archivo',  this.dato.archivo_name);
                if(this.id_tipo_transaccion){
                    return this.$store.dispatch('fiscal/cfd-sat/cargarXMLProveedor',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        var count = Object.keys(data).length;
                        if(count > 0 ){

                            this.dato = data;
                            this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', data);
                            this.cargado = true;
                        }else{
                            if(this.$refs.archivo.value !== ''){
                                this.$refs.archivo.value = '';
                                this.dato.archivo = null;
                            }
                            this.cargado = false;
                            this.cleanData();
                            swal('Cargar XML', 'Archivo sin datos válidos', 'warning')
                        }
                    }).catch(() => {
                        this.$refs.archivo.value = '';
                        this.dato.archivo = null;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
                }else {
                    swal( 'Seleccione tipo de transacción','Seleccione el tipo de transacción antes de seleccionar el archivo del CFDI', 'error');
                    this.$refs.archivo.value = '';
                    this.dato.archivo = null;
                }

            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.continuar()
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
            continuar() {
                this.$router.push({name: 'solicitud-recepcion-cfdi-create', params: { id_cfdi: this.dato.id }});
            },
        },
        computed: {
            tipos_transaccion(){
                return this.$store.getters['entrega-cfdi/tipo-transaccion/tipos'];
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
