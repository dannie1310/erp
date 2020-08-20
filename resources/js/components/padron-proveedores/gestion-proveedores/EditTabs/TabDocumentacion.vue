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
                                        <th >Estatus</th>
                                        <th >Documento</th>
                                        <th >Tipo Documento</th>

                                        <th >Obligatorio</th>
                                        <th >Sección</th>
                                        <th >Nombre Archivo</th>
                                        <th >Usuario Cargo</th>
                                        <th >Fecha Hora Carga</th>
                                        <th >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="archivo in archivos">
                                        <td><button  type="button" class="btn btn-sm " :class="{'btn-success': archivo.estatus == true}">
                                            <i class="fa fa-check" v-if="archivo.estatus"></i>
                                            <!-- <i class="fa fa-square-o" v-else></i> -->
                                            </button></td>
                                        <td :title="archivo.tipo_archivo_descripcion">{{archivo.tipo_archivo_descripcion_corta}}</td>
                                        <td>{{archivo.tipo_documento}}</td>
                                        <td>{{archivo.obligatorio}}</td>
                                        <td>{{archivo.seccion}}</td>
                                        <td>{{archivo.nombre_archivo_format}}</td>
                                        <td>{{archivo.registro}}</td>
                                        <td>{{archivo.fecha_registro_format}}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button @click="modalCarga(archivo)" type="button" class="btn btn-sm btn-outline-primary" title="Ver"  v-if="$root.can('actualizar_expediente_proveedor', true)"><i class="fa fa-upload"></i></button>
                                                <Documento v-bind:id="archivo.id" v-bind:rfc="empresa.rfc" v-if="archivo.nombre_archivo"></Documento>
                                                <button @click="eliminar(archivo)" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="archivo.nombre_archivo">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
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
                         <div class="col-md-12" v-if="archivo.tipo_archivo == 14">
                            <label for="id_tipo" class="col-sm-12 col-form-label">Seleccione si cuenta con listado de personal dado de alta ante el IMSS a través de SUA o si cuenta con empresa prestadora de servicios: </label>
                            <div class="col-sm-4 offset-4">
                                <div class="btn-group btn-group-toggle">
                                    <label class="btn btn-outline-secondary" :class="id_tipo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos" :key="llave">
                                        <input type="radio"
                                                class="btn-group-toggle"
                                                name="id_tipo"
                                                :id="'tipo' + llave"
                                                :value="llave"
                                                autocomplete="on"
                                                v-model.number="id_tipo">
                                                {{ tipo}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                         <div class="col-md-12" v-if="archivo.tipo_archivo == 14 && id_tipo == 2">
                                <b>Registrar empresa prestadora de servicios</b>
                        </div>
                        <div class="col-md-9" v-if="archivo.tipo_archivo == 14 && id_tipo == 2">
                            <label for="razon_social" class="col-lg-12 col-form-label">Razón Social</label>
                            <div class="col-lg-12">
                                    <input type="text" class="form-control"
                                            name="razon_social"
                                            v-model="razon_social"
                                            id="razon_social"
                                            placeholder="Razón Social"
                                            data-vv-as="Razón Social"
                                            v-validate="{required:id_tipo === 2?true:false}"
                                            :class="{'is-invalid': errors.has('razon_social')}" >
                                            <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3" v-if="archivo.tipo_archivo == 14 && id_tipo == 2">
                            <label for="rfc" class="col-lg-12 col-form-label">RFC</label>
                            <div class="col-lg-12">
                                    <input type="text" class="form-control"
                                            name="rfc"
                                            v-model="rfc"
                                            id="rfc"
                                            placeholder="RFC"
                                            data-vv-as="RFC"
                                            v-validate="{required:id_tipo === 2?true:false}"
                                            :class="{'is-invalid': errors.has('rfc')}" >
                                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                            </div>
                        </div>
                        </div>
                        <div class="row justify-content-between" v-if="archivo.tipo_archivo != 14 || id_tipo == 1">
                            <div class="col-md-12">
                                <label for="cargar_file" class="col-lg-12 col-form-label">Cargar {{archivo.tipo_archivo_descripcion}}</label>
                                <div class="col-lg-12">
                                    <input type="file" class="form-control" id="cargar_file"
                                            @change="onFileChange"
                                            row="3"
                                            v-validate="{required:true, ext: ['pdf'],  size: 5120}"
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
                        <button @click="validate" v-if="archivo.tipo_archivo != 14 || id_tipo == 1" type="button" class="btn btn-primary" >Cargar</button>
                        <button @click="validate" v-if="archivo.tipo_archivo == 14 && id_tipo == 2" type="button" class="btn btn-primary" >Registrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Documento from '../Documento';
export default {
    name: "tab-documentacion",
    props: ['id'],
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
        }
    },
    mounted() {
        // this.getSecciones();
    },
    methods: {
        createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;
            reader.onload = (e) => {
                vm.file = e.target.result;
            };
            reader.readAsDataURL(file);

        },
        onFileChange(e){
            this.file = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            if(e.target.id == 'cargar_file') {
                this.file_name = files[0].name;
                this.createImage(files[0]);
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
        getSecciones(){
            this.cargando = true;
            this.$store.commit('padronProveedores/ctg-seccion/SET_SECCIONES', null);
            return this.$store.dispatch('padronProveedores/ctg-seccion/index', {
                id: this.id,
                params: {include: [], sort: 'id', order: 'asc'}
            }).then(data => {
                this.$store.commit('padronProveedores/ctg-seccion/SET_SECCIONES', data.data);
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
        upload(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('archivo',  this.file);
            formData.append('archivo_nombre',  this.file_name);
            formData.append('id_empresa',  this.id);
            formData.append('rfc',  this.empresa.rfc);
            formData.append('id_archivo',  this.archivo.id);
            return this.$store.dispatch('padronProveedores/archivo/cargarArchivo', {
                data: formData,
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
                    if (this.archivo.tipo_archivo != 14 || this.id_tipo == 1) {
                        this.upload();
                    }
                    if (this.archivo.tipo_archivo == 14 && this.id_tipo == 2) {
                        this.registrarPrestadora();
                    }

                }
            });
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
        empresa(){
            return this.$store.getters['padronProveedores/empresa/currentEmpresa'];
        },
        archivos(){
            return this.$store.getters['padronProveedores/archivo/archivos'];
        }
    }

}
</script>

<style>

</style>
