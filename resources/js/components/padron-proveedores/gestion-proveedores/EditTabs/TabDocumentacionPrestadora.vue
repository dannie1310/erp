<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="empresa" >
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th class="icono" ></th>
                                        <th >Documento</th>
                                        <th >Tipo Documento</th>

                                        <th >Obligatorio</th>
                                        <th >Sección</th>
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
                                                        <button @click="modalCarga(archivo)" type="button" class="btn btn-sm btn-outline-primary" title="Cargar"  v-if="$root.can('actualizar_expediente_proveedor', true)"><i class="fa fa-upload"></i></button>
                                                        <Documento v-bind:id="archivo.id" v-if="archivo.nombre_archivo"></Documento>
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
                                <label for="cargar_file" class="col-lg-12 col-form-label">Cargar {{archivo.tipo_archivo_descripcion}}</label>
                                <div class="col-lg-12">
                                    <input type="file" class="form-control" id="cargar_file" multiple="multiple"
                                            @change="onFileChange"
                                            row="3"
                                            v-validate="{required:true, ext: validarExtensiones(archivo.tipo_archivo),  size: 5120}"
                                            name="cargar_file"
                                            data-vv-as="Cargar"
                                            ref="cargar_file"
                                            :class="{'is-invalid': errors.has('cargar_file')}"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} (PDF)</div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <button @click="validate" type="button" class="btn btn-primary" >Cargar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Documento from '../Documento';
export default {
    name: "tab-documentacion-prestadora",
    props: ['id_empresa', 'id_prestadora'],
    components:{Documento},
    data(){
        return{
            documentos:[],
            archivo:'',
            file:'',
            file_name:'',
            id_tipo: '',
            tipos: {
                2: "Prestadora de Servicios",
                1: "SUA"
            },
            razon_social:'',
            rfc:'',
            orden:[],
        }
    },
    mounted() {
        this.getArchivos();
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
        validateArchivos(nombres){
            if(nombres.length === 1){
                let split = nombres[0].nombre.split('.');
                if(split[split.length -1].toLowerCase() == 'zip'){
                    return true;
                }
            }
            return false;
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
        validarExtensiones(){
            return ['pdf', 'zip', 'jpg', 'jpeg', 'png'];
        },
        getArchivos(){
            this.cargando = true;
            this.$store.commit('padronProveedores/archivo-prestadora/SET_ARCHIVOS', null);
            return this.$store.dispatch('padronProveedores/archivo/getArchivos', {
                params: {
                    include: [], sort: 'id_tipo_archivo', order: 'asc',
                    id_empresa: this.id_empresa,
                    id_prestadora: this.id_prestadora,
                    }
            }).then(data => {
                this.$store.commit('padronProveedores/archivo-prestadora/SET_ARCHIVOS', data.data);
                this.setNumero();
            })
        },
        modalCarga(archivo){
            if(archivo.nombre_archivo != null){
                swal("¿Desea actualizar el documento cargado previamente?, se perdera el archivo anterior.", {
                        icon: "warning",
                        buttons: {
                            cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: true,
                        }
                        }
                    }) .then((value) => {
                        if (value) {
                            this.openModal(archivo);
                        }
                    });
            }else{
                this.openModal(archivo);
            }
        },
        openModal(archivo){
            this.archivo = archivo;
            this.$refs.cargar_file.value = '';
            this.file = null;
            this.file_name = '';
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        registrarPrestadora(){
            this.cargando = true;
            return this.$store.dispatch('padronProveedores/empresa/registrarPrestadora', {
                razon_social:this.razon_social,
                rfc:this.rfc,
                id_empresa:this.id,
            })
                .then(data => {
                    $(this.$refs.modal).modal('hide');
                    location.reload();
                }).finally( ()=>{

                });
        },
        setNumero(){
            let nom = 0;
            let self = this;
             if(this.areas && this.archivos){
                    this.areas.forEach(area => {
                        this.archivos.forEach(function (archivo, i) {
                            if(area.id == archivo.id_area){
                                nom = nom + 1;
                                self.orden[i] = nom;
                            }
                        });

                 });
             }
        },
        uploado(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('archivo',  this.file);
            formData.append('archivo_nombre',  this.file_name);
            formData.append('id_empresa',  this.empresa.prestadora.id);
            formData.append('rfc',  this.empresa.prestadora.rfc);
            formData.append('rfc_empresa',  this.empresa.rfc);
            formData.append('id_archivo',  this.archivo.id);
            return this.$store.dispatch('padronProveedores/archivo/cargarArchivo', {
                data: formData,
                config: {
                        params: { _method: 'POST'}
                    }
            }).then((data) => {
                this.$store.commit('padronProveedores/archivo-prestadora/UPDATE_ARCHIVO', data);
                $(this.$refs.modal).modal('hide');
            })
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
                this.$store.commit('padronProveedores/archivo-prestadora/UPDATE_ARCHIVO', data);
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
                this.$store.commit('padronProveedores/archivo-prestadora/UPDATE_ARCHIVO', data);
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
        validaArea(tipo){
            if(this.archivos){
                return this.archivos.some(el => el.id_area === tipo);
            }
        },
        eliminar(archivo){
            if(archivo.nombre_archivo != null) {
                return this.$store.dispatch('padronProveedores/archivo/eliminar', {
                    id: archivo.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('padronProveedores/archivo-prestadora/UPDATE_ARCHIVO', data);
                })
            }
        },
        verEspecificaciones(archivo, index){
            let data = {
                index:index+1,
                text:archivo.especificacion,
                id_area:archivo.id_area,
            };
            if(this.archivos[index+1].info){
                 this.$store.commit('padronProveedores/archivo-prestadora/DELETE_ARCHIVO', data);
                 this.orden.splice(index+1, 1);
            }else{
                this.orden.splice(index+1, 0, ['']);
                this.$store.commit('padronProveedores/archivo-prestadora/INSERT_ARCHIVO', data);
            }
        },
    },
    computed: {
        empresa(){
            return this.$store.getters['padronProveedores/empresa/currentEmpresa'];
        },
        archivos(){
            return this.$store.getters['padronProveedores/archivo-prestadora/archivos'];
        },
        areas(){
            return this.$store.getters['padronProveedores/ctg-area/areas'];
        }
    }

}
</script>

<style>

</style>
