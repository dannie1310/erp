<template>
    <span>
        <button  @click="openModal" type="button" class="btn btn-app btn-primary pull-right" title="Cargar" v-if="$root.can('cargar_archivos_transaccion', this.global) && $root.can(permiso, this.global)">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-upload" v-else></i>
            Subir Archivo
        </button>

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
                                <div class="form-group error-content">
                                    <label class="col-form-label">Archivos:</label>
                                    <input type="file" class="form-control" id="cargar_file"
                                           @change="onFileChange"
                                           row="3"
                                           v-validate="{required:true, ext: validarExtensiones(),  size: 102400}"
                                           name="cargar_file"
                                           data-vv-as="Cargar"
                                           ref="cargar_file"
                                           :class="{'is-invalid': errors.has('cargar_file')}"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} <span>(PDF, JPG, JPEG, PNG, ZIP)</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <div class="form-group error-content">
                                    <label class="col-form-label">Descripción:</label>
                                    <input
                                        type="text"
                                        id="descripcion_archivo"
                                        name="descripcion_archivo"
                                        data-vv-as="Descripción"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('descripcion_archivo')}"
                                        placeholder="Descripción"
                                        v-validate="{required:true, max:30}"
                                        v-model="descripcion"
                                        maxlength="30"
                                    />
                                    <div class="invalid-feedback" v-show="errors.has('descripcion_archivo')">{{ errors.first('descripcion_archivo') }}</div>
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
    </span>
</template>

<script>

export default {
    name: "upload",
    props: ['id','permiso','global', 'base_datos', 'id_obra'],

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
            cargando_imagenes: false
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
            if(size > 102400000){
                swal("El tamaño máximo permitido para la carga de archivos es de 100 MB.", {
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

    },

}
</script>

<style>

</style>
