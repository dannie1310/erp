<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="!documentos">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="documentos" >
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentos" name="documentos">
                                <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th class="icono"></th>
                                        <th >Documento</th>
                                        <th >Tipo Documento</th>
                                        <th >Usuario Cargo</th>
                                        <th >Fecha Hora Carga</th>
                                        <th >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="area in areas" v-if="validaArea(area.id)">
                                        <tr style="background-color:rgba(0, 0, 0, 0.3)">
                                            <td style="font-weight: bold" colspan="10">{{area.descripcion}}</td>
                                        </tr>
                                        <tr v-for="(archivo, i) in archivos" v-if="area.id == archivo.id_area">
                                            <template v-if="archivo.info">
                                                <td></td>
                                                <td></td>
                                                <td colspan="8" ><b>Especificación:</b> {{archivo.especificacion}}</td>
                                            </template>
                                            <template v-else>
                                                <td>{{orden[i]}}</td>
                                                <td>
                                                    <small class="label bg-success" v-if="archivo.estatus" style="padding: 3px 2px 3px 5px">
                                                        <i class="fa fa-check"></i>
                                                    </small>
                                                    <small class="label bg-danger" v-else-if="archivo.obligatorio == 1" style="padding: 2px 2px 2px 5px">
                                                        <i class="fa fa-times"></i>
                                                    </small>
                                                </td>
                                                <td :title="archivo.tipo_archivo_descripcion_larga">
                                                    <i @click="verEspecificaciones(archivo, i)" v-if="archivo.especificacion" title="Ver Especificaciones" class="fa fa-info-circle"></i>
                                                    {{archivo.tipo_archivo_descripcion}}
                                                </td>
                                                <td>{{archivo.tipo_documento}}</td>
                                                <td><i class="fa fa-check" v-if="archivo.obligatorio == 1"></i></td>
                                                <td>{{archivo.seccion}}</td>
                                                <td>{{archivo.registro}}</td>
                                                <td>{{archivo.fecha_registro_format}}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button  @click="modalCarga(archivo)" type="button" class="btn btn-sm btn-outline-primary" title="Cargar"  v-if="$root.can('actualizar_expediente_proveedor', true)"><i class="fa fa-upload"></i></button>
                                                        <Documento v-bind:id="archivo.id" v-if="archivo.nombre_archivo && archivo.extension == 'pdf'"></Documento>
                                                        <button v-if="archivo.extension && archivo.extension != 'pdf'" type="button" class="btn btn-sm btn-outline-success" title="Ver" @click="modalImagen(archivo)" :disabled="cargando_imagenes == true">
                                                            <span v-if="cargando_imagenes == true && id_archivo == archivo.id">
                                                                <i class="fa fa-spin fa-spinner"></i>
                                                            </span>
                                                            <span v-else>
                                                                <i class="fa fa-picture-o"></i>
                                                            </span>
                                                        </button>
                                                        <button @click="eliminar(archivo)" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="$root.can('eliminar_archivo_expediente', true) && archivo.nombre_archivo">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </template>

                                        </tr>
                                    </template>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> CARGAR ARCHIVO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <label for="cargar_file" class="col-lg-12 col-form-label">
                                     Cargar</label>
                                <div class="col-lg-12">
                                    <input type="file" class="form-control" id="cargar_file" multiple="multiple"
                                           @change="onFileChange"
                                           row="3"
                                           v-validate="{required:true, ext: validarExtensiones(),  size: 5120}"
                                           name="cargar_file"
                                           data-vv-as="Cargar"
                                           ref="cargar_file"
                                           :class="{'is-invalid': errors.has('cargar_file')}"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} <span>(PDF, JPG, JPEG, PNG, ZIP)</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                        <button @click="validate" type="button" class="btn btn-primary" :disabled="errors.count() > 0 || cargando == true">
                            <span v-if="cargando==true">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-save"></i>
                            </span> Guardar
                        </button>
                    </div>
                </div>
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
    name: "documentos",
    props: ['id'],
    components:{Documento, Imagen},
    data(){
        return{
            id_archivo:'',
            documentos:[],
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
        onFileChange(e){
            var size = 0;
            this.files = [];
            this.names = [];
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            if(e.target.id == 'cargar_file') {
                for(let i=0; i<files.length; i++) {
                    this.createImage(files[i]);
                    size = +size + +files[i].size;
                    this.names[i] = {
                        nombre: files[i].name,
                    };
                    this.createImage(files[i], i);
                }
            }
            if(size > 5120000){
                swal("El tamaño máximo permitido para la carga de archivos es de 5 MB.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: 'Enterado',
                            closeModal: true,
                        }
                    }
                }) .then(() => {
                    if(this.$refs.cargar_file !== undefined){
                        this.$refs.cargar_file.value = '';
                    }
                    this.names = [];
                    this.files = [];
                    $(this.$refs.modal).modal('hide');
                })
            }

        },
        find() {
            return this.$store.dispatch('padronProveedores/empresa/getDoctosGenerales', {
                id: this.id,
                params: {include: []}
            }).then(data => {
                this.documentos = data;
                this.cargando = false;
            })
        },
        modalCarga(archivo){
            this.openModal(archivo);
        },
        modalImagen(archivo){
            this.cargando_imagenes = true;
            this.id_archivo = archivo.id;
            this.imagenes = []
            this.getImagenes(archivo.id);
        },
        getImagenes(id) {
            return this.$store.dispatch('padronProveedores/archivo/getImagenes', {
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
        openModal(archivo){
            this.archivo = archivo;
            if(this.$refs.cargar_file !== undefined){
                this.$refs.cargar_file.value = '';
            }
            this.names = [];
            this.files = [];
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        validarExtensiones(){
            return ['pdf', 'zip', 'jpg', 'jpeg', 'png'];
        },
        upload(){
            var formData = new FormData();

            formData.append('id_empresa',  this.id);
            formData.append('rfc',  this.empresa.rfc);
            formData.append('id_archivo',  this.archivo.id);
            if(this.validateArchivos(this.names)){
                formData.append('archivo',  this.files[0].archivo);
                formData.append('archivo_nombre',  this.names[0].nombre);
                this.uploadZIP(formData);
            }else{
                formData.append('archivos',  JSON.stringify(this.files));
                formData.append('archivos_nombres',  JSON.stringify(this.names));
                this.uploadPDF(formData);
            }
        },
        uploadPDF(data){
            return this.$store.dispatch('padronProveedores/archivo/cargarArchivo', {
                data: data,
                config: {
                    params: { _method: 'POST', include:['integrantes']}
                }
            }).then((data) => {
                this.$store.commit('padronProveedores/archivo/UPDATE_ARCHIVO', data);
                $(this.$refs.modal).modal('hide');
            })
        },
        uploadZIP(data){
            return this.$store.dispatch('padronProveedores/archivo/cargarArchivoZIP', {
                data: data,
                config: {
                    params: { _method: 'POST'}
                }
            }).then((data) => {
                this.$store.commit('padronProveedores/archivo/UPDATE_ARCHIVO', data);
                $(this.$refs.modal).modal('hide');
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.upload();
                }
            });
        },
        validateArchivos(nombres){
            if(nombres.length === 1){
                let split = nombres[0].nombre.split('.');
                if(split[split.length -1].toLowerCase() == 'zip'){
                    return true;
                }
            }
            return false;
        },
        verEspecificaciones(archivo, index){
            let data = {
                index:index+1,
                text:archivo.especificacion,
                id_area:archivo.id_area,
            };
            if(this.archivos[index+1].info){
                this.$store.commit('padronProveedores/archivo/DELETE_ARCHIVO', data);
                this.orden.splice(index+1, 1);
            }else{
                this.orden.splice(index+1, 0, ['']);
                this.$store.commit('padronProveedores/archivo/INSERT_ARCHIVO', data);
            }
        },
        eliminar(archivo){
            if(archivo.nombre_archivo != null) {
                return this.$store.dispatch('padronProveedores/archivo/eliminar', {
                    id: archivo.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('padronProveedores/archivo/UPDATE_ARCHIVO', data);
                })
            }
        },
    },
    computed: {
        archivos(){
            return this.$store.getters['padronProveedores/archivo/archivos'];
        },
    }
}
</script>

<style>

</style>
