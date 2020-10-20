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
                    <div class="row" v-if="documentos" >
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped" id="documentos" name="documentos">
                                    <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th >Tipo Documento</th>
                                            <th >Documento</th>
                                            <th >Descripción</th>
                                            <th >Usuario Cargo</th>
                                            <th >Fecha Hora Carga</th>
                                            <th >Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(archivo, i) in documentos" >
                                            <template >
                                                <td>{{i+1}}</td>
                                                <td>{{archivo.tipo_archivo}}</td>
                                                <td>{{archivo.nombre}}</td>
                                                <td>{{archivo.descripcion}}</td>
                                                <td>{{archivo.registro}}</td>
                                                <td>{{archivo.fecha_registro_format}}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <Documento v-bind:url="url" v-bind:id="archivo.id" v-if="archivo.extension.toLowerCase() == 'pdf'"></Documento>
                                                        <button v-if="archivo.extension && archivo.extension.toLowerCase()  != 'pdf'" type="button" class="btn btn-sm btn-outline-success" title="Ver" @click="modalImagen(archivo)" :disabled="cargando_imagenes == true">
                                                            <span v-if="cargando_imagenes == true && id_archivo == archivo.id">
                                                                <i class="fa fa-spin fa-spinner"></i>
                                                            </span>
                                                            <span v-else>
                                                                <i class="fa fa-picture-o"></i>
                                                            </span>
                                                        </button>
                                                        <button @click="eliminar(archivo)" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="archivo.nombre && 1==0">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </template>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    props: ['id','cargar'],
    components:{Documento, Imagen},
    data(){
        return{
            url : '/api/archivo/{id}/documento?access_token='+this.$session.get('jwt')+'&db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra'),
            id_archivo:'',
            descripcion:'',
            archivo:'',
            imagenes:[],
            file:'',
            file_name:'',
            names:[],
            files:[],
            cargando: false,
            cargando_imagenes: false
        }
    },
    mounted() {
        this.find();
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
            return this.$store.dispatch('documentacion/archivo/getArchivosTransaccion', {
                id: this.id,
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando = false;
            })
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
            if(archivo.nombre_archivo != null) {
                return this.$store.dispatch('documentacion/archivo/eliminar', {
                    id: archivo.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('documentacion/archivo/UPDATE_ARCHIVO', data);
                })
            }
        },
    },
    computed: {
        documentos(){
            return this.$store.getters['documentacion/archivo/archivos'];
        },
    }
}
</script>

<style>

</style>
