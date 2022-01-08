<template>
    <span>
        <button  @click="openModal" type="button" class="btn btn-app btn-primary pull-right" title="Cargar" v-if="$root.can('cargar_archivos_transaccion', this.global) && $root.can(permiso, this.global)">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-upload" v-else></i>
            Subir Archivos
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> SUBIR ARCHIVOS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div >
                                    <div>
                                        <label class="col-form-label"><span><i class="fa fa-files-o"></i>Seleccione los archivos deseados</span></label>
                                    </div>
                                    <div>
                                        <div class="form-group error-content" >
                                            <input type="file" class="form-control" id="archivo" @change="onFileChange" multiple="multiple"
                                                   row="3"
                                                   v-validate="{ }"
                                                   name="archivos"
                                                   data-vv-as="Archivos a Enviar"
                                                   ref="archivos"
                                                   :class="{'is-invalid': errors.has('archivos')}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has('archivos')">{{ errors.first('archivos') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table  table-sm table-bordered" v-if="names.length>0">
                                    <tr>
                                        <th class="encabezado index_corto">
                                            #
                                        </th>

                                        <th class="encabezado">
                                            Nombre de Archivo
                                        </th>
                                        <th class="encabezado c300" >
                                            Tipo
                                        </th>
                                        <th class="encabezado c250" >
                                            Observaciones
                                        </th>
                                        <th class="encabezado icono">

                                        </th>
                                    </tr>

                                    <tr v-for="(archivo, i) in this.archivos">
                                        <td>{{i+1}}</td>
                                        <td>
                                            {{archivo.nombre}}
                                        </td>
                                        <td>
                                            <model-list-select
                                                :id="`tipo_archivo_${i}`"
                                                :name="`tipo_archivo_${i}`"
                                                option-value="id"
                                                option-text="descripcion"
                                                v-model="archivo.tipo"
                                                :list="tipos_archivo_enviar"
                                                :isError="archivo.errores_tipo">
                                                :placeholder="!cargando?'Seleccionar tipo de archivo':'Cargando...'">
                                            </model-list-select>
                                        </td>
                                        <td>
                                            <textarea
                                                :id="`observaciones_${i}`"
                                                :name="`observaciones_${i}`"
                                                class="form-control"
                                                v-model="archivo.observaciones"
                                                :data-vv-as="`Observaciones ${i+1}`"
                                                :class="{'is-invalid': archivo.errores_observacion}"
                                                rows="1"
                                            ></textarea>
                                        </td>
                                        <td style="text-align: center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarArchivo(i)" :disabled="archivos.length == 1" >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
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
    </span>
</template>

<script>

import {ModelListSelect} from 'vue-search-select';

export default {
    name: "upload-archivo-invitacion",
    props: ['id','permiso','global'],
    components: {ModelListSelect},

    data(){
        return{
            id_archivo:'',
            documentos:[],
            descripcion:'',
            archivo:'',
            imagenes:[],
            file:'',
            file_name:'',
            names:[],
            files:[],
            cargando: false,
            cargando_imagenes: false,
            archivos :[],
            tipos_archivo_enviar : [],
        }
    },
    mounted() {
        this.getTiposArchivoEnviar();
    },
    methods: {
        getTiposArchivoEnviar(){
            this.cargando = true;
            return this.$store.dispatch('contratos/invitacion/getTiposArchivo', {
                params:{
                    tipo : [1,3],
                    area: [1,3]
                }
            })
                .then(data => {
                    this.tipos_archivo_enviar = data;
                })
                .finally(()=>{
                    this.cargando = false;
                })
        },
        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.archivo = e.target.result;
                vm.files.push(e.target.result);
            };
            reader.readAsDataURL(file);
        },
        onFileChange(e){
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            let _self = this;

            for(let i=0; i<files.length; i++) {
                if(!this.names.includes(files[i].name))
                {
                    this.archivo_name = files[i].name;
                    this.createImage(files[i]);
                    this.names.push(files[i].name);
                    this.archivos.push({nombre:files[i].name, tipo:null, observaciones:"", errores_tipo: false, errores_observacion : false});
                }
            }
            this.$refs.archivos.value = '';
        },
        openModal(){
            if(this.$refs.cargar_file !== undefined){
                this.$refs.cargar_file.value = '';
            }
            this.names = [];
            this.files = [];
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        validarExtensiones(){
            return ['pdf', 'jpg', 'jpeg', 'png'];
        },
        upload(){
            var formData = new FormData();

            formData.append('id',  this.id);
            formData.append('descripcion',  this.descripcion);
            if(this.esZip(this.names)){
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
            if(this.global){
                data.append('base_datos',  this.base_datos);
                return this.$store.dispatch('documentacion/archivo/cargarArchivoSC', {
                    data: data,
                    config: {
                        params: { _method: 'POST'}
                    }
                }).then((data) => {

                }).finally(()=> {
                    $(this.$refs.modal).modal('hide');
                })
            } else {
                return this.$store.dispatch('documentacion/archivo/cargarArchivo', {
                    data: data,
                    config: {
                        params: { _method: 'POST'}
                    }
                }).then((data) => {

                }).finally(()=> {
                    $(this.$refs.modal).modal('hide');
                })
            }
        },
        uploadZIP(data){
            return this.$store.dispatch('documentacion/archivo/cargarArchivoZIP', {
                data: data,
                config: {
                    params: { _method: 'POST'}
                }
            }).then((data) => {
                this.$store.commit('documentacion/archivo/UPDATE_ARCHIVO', data);
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
        esZip(nombres){
            if(nombres.length === 1){
                let split = nombres[0].nombre.split('.');
                if(split[split.length -1].toLowerCase() == 'zip'){
                    return true;
                }
            }
            return false;
        },
        quitarArchivo(index){
            this.archivos.splice(index, 1);
            this.files.splice(index, 1);
            this.names.splice(index, 1);
        },
    },

}
</script>

<style>

</style>
