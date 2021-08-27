<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="cargando">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
                <span v-else>
                    <div class="row" v-if="archivos.length>0" >
                        <div class="col-md-12">
                            <div class="table-responsive">

                                <table class="table" id="documentos" name="documentos">
                                    <tbody>
                                        <template v-for="(archivo, i) in archivos" >
                                            <tr v-if="i ==0" style="background-color: #ddd">
                                                <td class="index_corto">#</td>
                                                <td>Tipo Documento</td>
                                                <td >Documento</td>
                                                <td >Descripción</td>
                                                <td >Usuario Cargo</td>
                                                <td class="fecha_hora">Fecha Hora Carga</td>
                                                <td >Acciones</td>
                                            </tr>
                                            <tr v-if="i ==0">
                                                <td colspan="2"><strong><i :class="archivo.icono_transaccion"></i>{{archivo.tipo_transaccion}} {{archivo.folio_transaccion}}</strong></td>
                                                <td colspan="5">{{archivo.observaciones_transaccion}}</td>
                                            </tr>
                                            <tr v-else-if="archivo.id_transaccion != archivos[i-1].id_transaccion">
                                                <td colspan="2"><strong><i :class="archivo.icono_transaccion"></i>{{archivo.tipo_transaccion}} {{archivo.folio_transaccion}}</strong></td>
                                                <td colspan="5">{{archivo.observaciones_transaccion}}</td>
                                            </tr>

                                            <tr  >
                                                <td>{{i+1}}</td>
                                                <td>{{archivo.tipo_archivo}}</td>
                                                <td>{{archivo.nombre}}</td>
                                                <td>{{archivo.descripcion}}</td>
                                                <td>{{archivo.registro}}</td>
                                                <td>{{archivo.fecha_registro_format}}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <Documento v-bind:url="url" v-bind:metodo = "metodo" v-bind:base_datos="base_datos_url" v-bind:id_obra="id_obra_url" v-bind:id="archivo.id" v-if="archivo.extension.toLowerCase() == 'pdf'"></Documento>
                                                        <button v-if="archivo.extension && archivo.extension.toLowerCase()  != 'pdf'" type="button" class="btn btn-sm btn-outline-success" title="Ver" @click="modalImagen(archivo)" :disabled="cargando_imagenes == true">
                                                            <span v-if="cargando_imagenes == true && id_archivo == archivo.id">
                                                                <i class="fa fa-spin fa-spinner"></i>
                                                            </span>
                                                            <span v-else>
                                                                <i class="fa fa-picture-o"></i>
                                                            </span>
                                                        </button>
                                                        <button @click="eliminar(archivo)" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="archivo.nombre && archivo.eliminable" :disabled="eliminando_imagenes">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="row" v-else >
                        No hay archivos cargados.
                    </div>
                </span>
            </div>
        </div>

        <div class="modal fade" ref="modalImagen" tabindex="-1" role="dialog" aria-labelledby="modal">
            <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" id="mdialTamanio">
                <div class="modal-content" >
                    <Imagen v-bind:imagenes="imagenes" v-bind:id="id_archivo"></Imagen>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Documento from './Documento';
import Imagen from './Imagen';
export default {
    name: "List",
    props: ['id','tipo','cargar','relacionadas', 'sin_contexto', 'id_obra', 'base_datos'],
    components:{Documento, Imagen},
    data(){
        return{
            url : '/api/archivo/{id}/{metodo}?access_token='+this.$session.get('jwt')+'&db={base_datos}&idobra={id_obra}',
            id_archivo:'',
            descripcion:'',
            archivo:'',
            imagenes:[],
            file:'',
            file_name:'',
            names:[],
            files:[],
            cargando: false,
            cargando_imagenes: false,
            eliminando_imagenes : false,
            id_obra_url : '',
            base_datos_url : '',
            metodo : '',
        }
    },
    mounted() {
        this.find();
        if(this.base_datos)
        {
            this.base_datos_url = this.base_datos;
        }else{
            this.base_datos_url = this.$session.get('db');
        }
        if(this.id_obra)
        {
            this.id_obra_url = this.id_obra;
        }else{
            this.id_obra_url = this.$session.get('id_obra');
        }
        if(this.sin_contexto)
        {
            this.metodo = 'documento-sc'
        }
        else  {
            this.metodo = 'documento';
        }
    },
    methods: {
        createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;
            reader.onload = (e) => {
                vm.files[tipo] = {archivo:e.target.result}
            };
            reader.readAsDataURL(file);
        },
        find() {
            this.cargando = true;
            if(!this.sin_contexto){
                if(!this.relacionadas){
                    return this.$store.dispatch('documentacion/archivo/getArchivosTransaccion', {
                        id: this.id,
                        params: {include: []}
                    }).then(data => {
                    }).finally(()=> {
                        this.cargando = false;
                    })
                }else{
                    return this.$store.dispatch('documentacion/archivo/getArchivosRelacionadosTransaccion', {
                        id: this.id,
                        tipo: this.tipo,
                        params: {include: []}
                    }).then(data => {
                    }).finally(()=> {
                        this.cargando = false;
                    })
                }
            }else {
                if(!this.relacionadas){
                    let _self = this;
                    return this.$store.dispatch('documentacion/archivo/getArchivosTransaccionSC', {
                        id: this.id,
                        id_obra : _self.id_obra,
                        base_datos : _self.base_datos
                    }).then(data => {
                    }).finally(()=> {
                        this.cargando = false;
                    })
                }else{
                    return this.$store.dispatch('documentacion/archivo/getArchivosRelacionadosTransaccionSC', {
                        id: this.id,
                        'tipo': this.tipo,
                        data: {id_obra : this.id_obra, base_datos : this.base_datos}
                    }).then(data => {
                    }).finally(()=> {
                        this.cargando = false;
                    })
                }
            }
        },
        modalImagen(archivo){
            this.cargando_imagenes = true;
            this.id_archivo = archivo.id;
            this.imagenes = []
            this.getImagenes(archivo.id);
        },
        getImagenes(id) {
            return this.$store.dispatch('documentacion/archivo/getImagenes', {
                id: id,
                params: {include: []}
            }).then(data => {
                this.imagenes = data;
            }).finally( ()=>{
                this.cargando_imagenes = false;
                $(this.$refs.modalImagen).appendTo('body');
                $(this.$refs.modalImagen).modal('show');
            })
        },

        eliminar(archivo){

            if(this.sin_contexto)
            {
                this.eliminando_imagenes = true;
                let _self = this;
                return this.$store.dispatch('documentacion/archivo/eliminarSC', {
                    id: archivo.id,
                    id_obra : _self.id_obra,
                    base_datos : _self.base_datos,
                }).then(data => {
                    //this.$store.commit('documentacion/archivo/DELETE_ARCHIVO', data);
                }).finally( ()=>{
                    this.eliminando_imagenes = false;
                })
            }else {
                this.eliminando_imagenes = true;
                let _self = this;
                return this.$store.dispatch('documentacion/archivo/eliminar', {
                    id: archivo.id,
                    id_obra : _self.id_obra,
                    base_datos : _self.base_datos,
                }).then(data => {
                    //this.$store.commit('documentacion/archivo/DELETE_ARCHIVO', data);
                }).finally( ()=>{
                    this.eliminando_imagenes = false;
                })
            }
        },
    },
    computed: {
        archivos(){
            return this.$store.getters['documentacion/archivo/archivos'];
        },
        transaccion(){
            return this.$store.getters['documentacion/archivo/transaccion'];
        },
    }
}
</script>

<style>

</style>
