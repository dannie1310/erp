<template>
    <span>
        <button  @click="openModal" type="button" class="btn btn-success pull-right" title="Agregar Archivo" >
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Agregar Archivo
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> AGREGAR ARCHIVO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <div class="form-group error-content">
                                    <label class="col-form-label">Tipo de Archivo:</label>
                                    <model-list-select v-if="tipos_archivo"
                                        name="tipo"
                                        id="tipo"
                                        placeholder="Seleccionar o buscar"
                                        data-vv-as="Tipo de Archivo"
                                        v-validate="{required: true}"
                                        v-model="id_tipo_archivo"
                                        option-value="id"
                                        option-text="descripcion"
                                        :list="tipos_archivo"
                                        :isError="errors.has('tipo')"
                                    ></model-list-select>
                                    <input v-else class="form-control" value="Cargando..." readonly="readonly"/>

                                    <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <div class="form-group error-content">
                                    <label class="col-form-label">Archivo:</label>
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
                                    <label class="col-form-label">Observaciones:</label>
                                    <input
                                        type="text"
                                        id="descripcion_archivo"
                                        name="descripcion_archivo"
                                        data-vv-as="Observaciones"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('descripcion_archivo')}"
                                        placeholder="Observaciones"
                                        v-validate="{max:1000}"
                                        v-model="observaciones"
                                        maxlength="1000"
                                    />
                                    <div class="invalid-feedback" v-show="errors.has('descripcion_archivo')">{{ errors.first('descripcion_archivo') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                        <button @click="validate" type="button" class="btn btn-primary" :disabled="errors.count() > 0 || cargando == true">
                            <span v-if="cargando==true">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-plus"></i>
                            </span> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from "vue-search-select";

export default {
    name: "add-proveedor",
    components: {ModelListSelect},
    props: ["id_cfdi"],
    data(){
        return{
            id:'',
            id_archivo:'',
            tipo:'',
            documentos:[],
            observaciones:'',
            archivo:'',
            imagenes:[],
            file:'',
            file_name:'',
            names:[],
            files:[],
            cargando: false,
            cargando_imagenes: false,
            id_tipo_archivo:null
        }
    },
    mounted() {
        this.getTiposArchivo();
    },

    methods: {
        getTiposArchivo(){
            this.$store.commit('entrega-cfdi/tipo-archivo/SET_TIPOS', null);
            return this.$store.dispatch('entrega-cfdi/tipo-archivo/index', {
                params: {include: [], sort: 'descripcion', order: 'asc'}
            }).then(data => {
                this.$store.commit('entrega-cfdi/tipo-archivo/SET_TIPOS', data);
                this.cargando = false;
            })
        },

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
            return ['pdf'/*, 'jpg', 'jpeg', 'png'*/];
        },
        upload(){
            var formData = new FormData();
            formData.append('id_cfdi',  this.id_cfdi);
            formData.append('id_tipo_archivo',  this.id_tipo_archivo);
            formData.append('id_archivo',  null);
            formData.append('observaciones',  this.observaciones);
            formData.append('archivos',  JSON.stringify(this.files));
            formData.append('archivos_nombres',  JSON.stringify(this.names));
            this.uploadPDF(formData);
        },
        uploadPDF(data){
            return this.$store.dispatch('entrega-cfdi/archivo/agregarArchivo', {
                data: data,
                config: {
                    params: { _method: 'POST'}
                }
            }).then((data) => {

            }).finally(()=> {
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
    },

    computed: {
        tipos_archivo(){
            return this.$store.getters['entrega-cfdi/tipo-archivo/tipos'];
        },
    }

}
</script>

<style>

</style>
