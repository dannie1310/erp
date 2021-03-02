<template>
    <span>
        <div class="card" v-if="cargado">
            <div class="card-header">
                <h5>Datos del CFDI</h5>
            </div>
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Emisión:</label>
                                <input class="form-control" v-model="cfdi.fecha_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Serie y Folio:</label>
                                <input class="form-control" v-model="cfdi.referencia" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Tipo:</label>
                                <input class="form-control" v-model="cfdi.tipo_comprobante" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label >UUID:</label>
                                <input class="form-control" v-model="cfdi.uuid" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                            <label >Empresa:</label>
                                <input class="form-control" v-model="cfdi.empresa.razon_social" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >RFC:</label>
                                <input class="form-control" v-model="cfdi.empresa.rfc" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Retenidos:</label>
                                <input class="form-control" v-model="cfdi.impuestos_retenidos_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Trasladados:</label>
                                <input class="form-control" v-model="cfdi.impuestos_trasladados_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Total:</label>
                                <input class="form-control" v-model="cfdi.total_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Moneda:</label>
                                <input class="form-control" v-model="cfdi.moneda" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Tipo de Cambio:</label>
                                <input class="form-control" v-model="cfdi.tipo_cambio" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                </span>
            </div>
        </div>
        <form role="form" @submit.prevent="validate">
            <div class="card" v-if="cargado">
                <div class="card-header">
                    <h5>Datos para la Solicitud de Recepción</h5>
                </div>
                <div class="card-body">
                    <span>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >Proyecto:</label>
                                    <treeselect
                                        :class="{error: errors.has('proyecto')}"
                                        :async="true"
                                        :load-options="loadOptions"
                                        v-model="proyecto"
                                        v-validate="{required: true}"
                                        data-vv-as="Proyecto"
                                        name="proyecto"
                                        loadingText="Cargando"
                                        searchPromptText="Escriba para buscar..."
                                        noResultsText="Sin Resultados"
                                        placeholder="-- Buscar Proyecto -- "
                                    >
                                    </treeselect>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >Contacto HI:</label>
                                    <input class="form-control"
                                           name = "contacto"
                                           data-vv-as="Contacto"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('contacto')}"
                                           v-model="contacto"  />
                                    <div class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >Correo para recibir notificaciones:</label>
                                    <input class="form-control"
                                           v-model="correo"
                                           name = "correo"
                                           data-vv-as="Correo"
                                           :class="{'is-invalid': errors.has('correo')}"
                                           v-validate="{required: true, email:true}"/>
                                    <div class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group error-content">
                                    <label >Observaciones:</label>
                                    <textarea class="form-control"

                                              v-model="observaciones"
                                              name = "observaciones"
                                              data-vv-as="Observaciones"
                                              id = "observaciones"
                                              :class="{'is-invalid': errors.has('observaciones')}"

                                              v-validate="{required: true}"
                                    ></textarea>
                                    <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                </div>
                            </div>
                        </div>
                    </span>
                </div>
                <div class="card-footer">
                    <span class="pull-right">
                        <button type="button" class="btn btn-secondary" >
                            <i class="fa fa-angle-left"></i>Regresar
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 " >
                            <i class="fa fa-save"></i> Registrar
                        </button>
                    </span>
                </div>
            </div>
        </form>
    </span>


</template>

<script>

    export default {
        name: "solicitud-recepcion-cfdi-create",
        props: ["id_cfdi"],
        data() {
            return {
                cargando:true,
                cargado:false,
                correo:'',
                contacto:'',
                observaciones:'',
                proyecto:null,
                id_cfdi_solicitar:''
            }
        },
        mounted() {
            this.find();

        },
        computed: {
            cfdi(){
                return this.$store.getters['fiscal/cfd-sat/currentCFDSAT'];
            },
        },
        methods:{
            find(){
                this.cargado = false;
                this.id_cfdi_solicitar = this.id_cfdi;
                if(this.$store.getters['fiscal/cfd-sat/currentCFDSAT'] == null){
                    this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', null);
                    return this.$store.dispatch('fiscal/cfd-sat/find', {
                        id: this.id_cfdi,
                        params:{}
                    }).then(data => {
                        this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', data);
                    }).finally(()=>{
                        this.cargado = true;
                    });
                } else {
                    this.cargado = true;
                }
            },
            loadOptions({ action, searchQuery, callback }) {
                if(searchQuery.length >= 3) {
                    return this.$store.dispatch('catalogos/proyecto/index', {
                        params: {
                            search: searchQuery,
                            scope: ['rfc:'+this.cfdi.rfc_receptor, "activa"],
                            limit: 20,
                            sort: 'nombre',
                            order: 'ASC'
                        }
                    })
                    .then(data => {
                        const options = data.data.map(i => ({
                            id: i.id,
                            label: i.nombre
                        }))
                        callback(null, options)
                    })
                }
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('entrega-cfdi/solicitud-recepcion-cfdi/store', this.$data)
                .then(data => {
                    this.$emit('created', data);
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
