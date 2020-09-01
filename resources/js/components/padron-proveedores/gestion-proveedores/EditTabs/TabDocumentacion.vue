<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="!areas">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="areas" >
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentos" name="documentos">
                                <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th class="icono"></th>
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
                        <div class="row" v-if="archivo.tipo_archivo == id_archivo_sua">
                            <div class="col-md-12" >
                                <label class="col-sm-12 col-form-label">
                                    <i class="fa fa-info-circle"></i>
                                    Indique si cuenta con personal propio o si cuenta con una empresa prestadora de servicios: </label>
                                <div class="col-sm-6 offset-3">
                                    <div class="btn-group btn-group-toggle">
                                        <label class="btn btn-outline-secondary" :class="id_tipo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos" :key="llave">
                                            <i :class="llave==1 ?'fa fa-users':'fa fa-building'"></i>
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
                        </div>

                        <div class="row" v-if="archivo.tipo_archivo == id_archivo_sua && id_tipo == 2">
                            <div class="col-md-12" >
                                <br>
                                <hr>
                                <b><i class="fa fa-building"></i> Registrar empresa prestadora de servicios</b>
                            </div>
                            <div class="col-md-12">
                                <label for="razon_social" class="col-lg-12 col-form-label">Razón Social:</label>
                                <div class="col-lg-12">
                                    <input type="text" class="form-control"
                                           style="text-transform:uppercase;"
                                           name="razon_social"
                                           v-model="razon_social"
                                           id="razon_social"
                                           data-vv-as="'Razón Social'"
                                           v-validate="{required:id_tipo === 2?true:false, min:6}"
                                           :class="{'is-invalid': errors.has('razon_social')}" >
                                <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label for="rfc" class="col-lg-12 col-form-label">RFC:</label>
                                <div class="col-lg-12">
                                    <input type="text" class="form-control"
                                           name="rfc"
                                           v-model="rfc"
                                           id="rfc"
                                           data-vv-as="'RFC'"
                                           v-validate="{required:id_tipo === 2?true:false, min:6}"
                                           :class="{'is-invalid': errors.has('rfc')}" >
                                    <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="nss" class="col-form-label">Registro Patronal:</label>
                                <div class="col-lg-12">
                                    <input class="form-control"
                                           name="nss"
                                           data-vv-as="'Registro Patronal'"
                                           v-model="nss"
                                           v-validate="{ required:id_tipo === 2?true:false, min:11, length:11}"
                                           id="nss"
                                           :class="{'is-invalid': errors.has('nss')}"
                                           :maxlength="11"/>
                                    <div class="invalid-feedback" v-show="errors.has('nss')">{{ errors.first('nss') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between" v-else-if="archivo.tipo_archivo == id_archivo_sua && id_tipo == 1">
                            <div class="col-md-12">
                                <br>
                                <hr>
                                <label for="cargar_file" class="col-lg-12 col-form-label">
                                    <i class="fa fa-users"></i> Cargar {{archivo.tipo_archivo_descripcion}}</label>
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
                        <div class="row justify-content-between" v-else-if="archivo.tipo_archivo != id_archivo_sua">
                            <div class="col-md-12">
                                <label for="cargar_file" class="col-lg-12 col-form-label">
                                     Cargar {{archivo.tipo_archivo_descripcion}}</label>
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
                                    <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} <span v-if="archivo.tipo_archivo == id_pago_sua">(PDF o ZIP)</span><span v-else>(PDF)</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                        <button @click="validate" v-if="archivo.tipo_archivo != id_archivo_sua || id_tipo == 1" type="button" class="btn btn-primary" :disabled="errors.count() > 0">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button @click="validate" v-if="archivo.tipo_archivo == id_archivo_sua && id_tipo == 2" type="button" class="btn btn-primary" :disabled="errors.count() > 0">
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
            names:[],
            files:[],
            id_tipo: '',
            tipos: {
                2: "Prestadora de Servicios",
                1: "Personal Propio"
            },
            razon_social:'',
            rfc:'',
            nss: '',
            orden:[],
            id_archivo_sua:15,  /// CAMBIAR SOLO AQUI EN CASO QUE CAMBIE EL ID DE "Listado de personal dado de alta ante el IMSS a través de SUA" EN LA BBDD
            id_pago_sua:34,  /// CAMBIAR SOLO AQUI EN CASO QUE CAMBIE EL ID DE "Pago SUA" EN LA BBDD
            cargando: false
        }
    },
    mounted() {
        this.getAreas();
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
            this.file = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            if(files.length > 15){
                swal("El limite de archivos PDF permitidos es de 15", {
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
                swal("El tamaño máximo permitido es de 5 Mb.", {
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
        getAreas(){
            this.$store.commit('padronProveedores/ctg-area/SET_AREAS', null);
            return this.$store.dispatch('padronProveedores/ctg-area/index', {
                id: this.id,
                params: {include: [], sort: 'id', order: 'asc'}
            }).then(data => {
                this.$store.commit('padronProveedores/ctg-area/SET_AREAS', data);
                this.setNumero();
                this.cargando = false;
            })
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
            if(this.$refs.cargar_file !== undefined){
                this.$refs.cargar_file.value = '';
            }
            this.file = null;
            this.file_name = '';
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        validarPrestadora(){
            return this.$store.dispatch('padronProveedores/empresa/validarPrestadora', {
                rfc:this.rfc,
            })
                .then(data => {
                   if(data.asociacion){
                        swal("El RFC ingresado pertenece a la empresa prestadora (" + data.razon_social +")¿Desea asociarla?", {
                        icon: "warning",
                        buttons: {
                            cancel: {
                            text: 'No',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Asociar',
                            closeModal: true,
                            }
                            }
                        }) .then((value) => {
                            if (value) {
                                this.registrarPrestadora(data.asociacion);
                            }
                        });
                   }else{
                       this.registrarPrestadora(data.asociacion);
                   }
                }).finally( ()=>{

                });

        },
        validarExtensiones(){
            return ['pdf', 'zip'];
        },
        registrarPrestadora(asociacion){
            this.cargando = true;
            this.rfc = this.rfc.toUpperCase();
            this.razon_social = this.razon_social.toUpperCase();
            return this.$store.dispatch('padronProveedores/empresa/registrarPrestadora', {
                razon_social:this.razon_social,
                rfc:this.rfc,
                nss:this.nss,
                id_empresa:this.id,
                id_archivo_sua:this.id_archivo_sua,
                asociacion:asociacion,
            })
                .then(data => {
                    this.cargando = false;
                    $(this.$refs.modal).modal('hide');
                    location.reload();
                }).finally( ()=>{
                    this.cargando = false;
                });
        },
        upload(){
            var formData = new FormData();
            formData.append('archivos',  JSON.stringify(this.files));
            formData.append('archivos_nombres',  JSON.stringify(this.names));
            formData.append('id_empresa',  this.id);
            formData.append('rfc',  this.empresa.rfc);
            formData.append('id_archivo',  this.archivo.id);
            let split = this.file_name.split('.');
            if(split[split.length -1].toLowerCase() == 'zip'){
                this.uploadZIP(formData);
            }else{
                this.uploadPDF(formData);
            }
        },
        uploadPDF(data){
            return this.$store.dispatch('padronProveedores/archivo/cargarArchivo', {
                data: data,
                config: {
                        params: { _method: 'POST'}
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
                    if(this.archivo.tipo_archivo != this.id_archivo_sua || this.id_tipo == 1){
                        this.upload();
                    }
                    if(this.archivo.tipo_archivo == this.id_archivo_sua && this.id_tipo == 2){
                        this.validarPrestadora();
                    }
                }
            });
        },
        validaArea(tipo){
            if(this.archivos){
                return this.archivos.some(el => el.id_area === tipo);
            }
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
        empresa(){
            return this.$store.getters['padronProveedores/empresa/currentEmpresa'];
        },
        archivos(){
            return this.$store.getters['padronProveedores/archivo/archivos'];
        },
        areas(){
            return this.$store.getters['padronProveedores/ctg-area/areas'];
        }
    }
}
</script>

<style>

</style>
