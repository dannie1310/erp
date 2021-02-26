<template>
    <span>
        <div class="row">
            <div class="col-md-4">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input button" id="con_nota_credito" v-model="dato.con_nota_credito" >
                    <label class="custom-control-label" for="con_nota_credito">Aplicar Nota de Crédito</label>
                </div>
            </div>
        </div>
        <div class="row" >
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
        <div class="row" v-if="dato.con_nota_credito">
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

                </div>
            </div>
            <div class="col-md-2">
                <!--Referencia-->
                    <div class="form-group error-content">
                        <label for="referencia">Folio:</label>

                    </div>
            </div>


        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="form-group error-content">
                    <label for="fecha">Emisión:</label>

                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group error-content">
                    <label for="fecha">Vencimiento:</label>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group error-content">
                    <label for="total">Total:</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group error-content">
                    <label for="total">Moneda:</label>
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
    </span>

</template>

<script>

    export default {
        name: "solicitud-recepcion-cfdi-create",
        data() {
            return {
                cargando:true,
                dato:{
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
