<template>
    <span v-if="cfdi">
        <span >
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td>Referencia CFDI:</td>
                                            <td><b><CFDI v-bind:id="cfdi.id" v-bind:txt="cfdi.referencia"></CFDI></b></td>
                                        </tr>
                                        <tr>
                                            <td>Total CFDI:</td>
                                            <td style="text-align: right"><b>{{cfdi.total_format}}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </span>
                </div>
            </div>
            <br>
            <form role="form" @submit.prevent="validate">
                <div class="card" v-if="cargado">
                    <div class="card-header">
                        <h5>Datos para la Solicitud de Revisión</h5>
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

                </div>

                <soporte-documental v-bind:id_cfdi="id_cfdi" v-bind:configuracion="{agregar:true, eliminar:true, eliminar_tipo:true, reemplazar:true, cargar:true}"></soporte-documental>
                <div class="pull-right" style="padding-bottom: 48px">
                    <button type="button" class="btn btn-secondary" v-on:click="regresar" >
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" >
                        <i class="fa fa-save"></i> Registrar
                    </button>
                </div>
            </form>
        </span>
    </span>
    <span v-else>
        <div class="card" >
            <div class="card-body">
                <div >
                    <div class="row" >
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                               <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

    import SoporteDocumental from "../SoporteDocumental";
    import CFDI from "../../fiscal/cfd/cfd-sat/CFDI";
    export default {
        name: "solicitud-recepcion-cfdi-create",
        components: {SoporteDocumental, CFDI},
        props: ["id_cfdi"],
        data() {
            return {
                url : '/api/entrega-cfdi/archivo/{id}/documento?access_token='+this.$session.get('jwt')+'&db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra'),
                cargando:true,
                cargado:false,
                cargando_archivo:false,
                eliminando : false,
                correo:'',
                contacto:'',
                observaciones:'',
                proyecto:null,
                id_cfdi_solicitar:'',
                obligatorios_completos: false,
            }
        },
        mounted() {
            this.find();
        },
        methods:{
            find(){
                this.cargado = false;
                this.id_cfdi_solicitar = this.id_cfdi;
                if(this.$store.getters['fiscal/cfd-sat/currentCFDSAT'] == null){
                    this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', null);
                    return this.$store.dispatch('fiscal/cfd-sat/find', {
                        id: this.id_cfdi,
                        params:{include: ['archivos']}
                    }).then(data => {
                        this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', data);
                        this.$store.commit('entrega-cfdi/archivo/SET_ARCHIVOS', data.archivos.data);
                    }).finally(()=>{
                        //this.getArchivos();
                        this.cargado = true;
                    });
                } else {
                    this.$store.commit('entrega-cfdi/archivo/SET_ARCHIVOS', this.cfdi.archivos.data);
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
                        if(this.obligatorios_completos){
                            this.store()
                        } else {
                            swal(  'Archivos Faltantes', 'No puede realizar el registro de la solicitud si hay archivos obligatorios pendientes de cargar', 'error');
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('entrega-cfdi/solicitud-recepcion-cfdi/store', this.$data)
                .then(data => {
                    this.$emit('created', data);
                    this.$router.push({name: 'entrega-cfdi'});
                }).finally( ()=>{
                    this.cargando = false;
                });
            },
            regresar() {
                this.$router.push({name: 'seleccionar-cfdi'});
            },
            eliminar(archivo){
                this.eliminando = true;
                return this.$store.dispatch('entrega-cfdi/archivo/eliminar', {
                    params: {id: archivo.id}
                }).then(data => {
                    //this.$store.commit('documentacion/archivo/DELETE_ARCHIVO', data);
                }).finally( ()=>{
                    this.eliminando = false;
                })
            },
            eliminarTipo(archivo){
                this.eliminando = true;
                return this.$store.dispatch('entrega-cfdi/archivo/eliminarTipo', {
                    id: archivo.id,
                    params: {}
                }).then(data => {
                    //this.$store.commit('documentacion/archivo/DELETE_ARCHIVO', data);
                }).finally( ()=>{
                    this.eliminando = false;
                })
            },
        },
        computed: {
            cfdi(){
                return this.$store.getters['fiscal/cfd-sat/currentCFDSAT'];
            },
            archivos(){
                return this.$store.getters['entrega-cfdi/archivo/archivos'];
            }
        },
        watch:{
            archivos:{
                deep: true,
                immediate : true,
                handler(archivos) {
                    let self = this
                    let completos = true;
                    archivos.forEach(function (archivo, i) {
                        if(archivo.obligatorio && !archivo.estatus){
                            self.obligatorios_completos = false;
                            completos = false;
                        }
                    });
                    if(completos){
                        self.obligatorios_completos = true;
                    }
                },
            }
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
