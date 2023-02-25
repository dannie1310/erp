<template>
    <span>
        <button @click="load" class="btn btn-app pull-right" title="Cargar Layout" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-upload" v-else></i>
            Cargar Cuentas
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-upload"></i> Cargar Cuentas de Balanza por Layout</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-primary alert-dismissible fade show">
                                        <i class="fa fa-info-circle"></i> El archivo de excel solo debe contener el código y nombre de las cuentas consideradas <strong>deducibles</strong>.
                                        <br>En columna A poner el código de cuenta, en columna B poner el nombre de la cuenta (ver imagen):
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                     <img src="../../../../../img/fiscal/layout_cuentas.jpg" class="rounded" alt="Formato de carga de Layout">
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout:</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               accept=".xlsx"
                                               @change="onFileChange"
                                               v-validate="{ ext: ['xlsx']}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (csv)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Seleccionar Empresa Contpaq:</label>
                                    <div class="col-lg-12">
                                        <model-list-select
                                            :disabled="cargando"
                                            name="id_empresa"
                                            v-model="id_empresa"
                                            option-value="id"
                                            option-text="nombre"
                                            :custom-text="nombreAlias"
                                            :list="empresas"
                                            :placeholder="!cargando?'Seleccionar o buscar empresa':'Cargando...'"
                                            :isError="errors.has(`id_empresa`)">
                                        </model-list-select>
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (csv)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal"><i class="fa fa-times"></i>Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="validate" :disabled="!file"><i class="fa fa-upload"></i>Cargar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </span>
</template>

<script>
import {ModelListSelect} from "vue-search-select";
    export default {
        name: "carga-cuentas-balanza",
        components: {ModelListSelect},
        props: ['id'],
        data() {
            return {
                cargando: false,
                data: null,
                file: null,
                id_empresa: '',
                empresas: [],
                nombre: '',
            }
        },
        mounted(){
            this.getEmpresas();
        },
        methods:{
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        this.cargarLayout()
                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file = null;
                        }
                        this.$validator.errors.clear();
                        swal('¡Error!', 'Error archivos de entrada invalidos.', 'error')
                    }
                });
            },
            load() {
                this.$refs.carga_layout.value = '';
                this.file = null;
                this.$validator.errors.clear();

                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            cerrarModal(event) {
                this.$refs.carga_layout.value = '';
                this.file = null;
                this.$validator.errors.clear();
                this.id_empresa = '';
                $(this.$refs.modal).modal('hide')
            },
            cargarLayout(){
                var formData = new FormData();
                formData.append('file',  this.file);
                formData.append('name', this.nombre);
                formData.append("id_empresa", this.id_empresa);
                return this.$store.dispatch('fiscal/informes/cargaCuentasBalanzaPorLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        this.$emit('change', data);
                    }).finally(() => {
                        this.$refs.carga_layout.value = '';
                        this.file = null;
                        this.file_name = '';
                        this.$validator.errors.clear();
                        setTimeout(() => {
                            $(this.$refs.modal).modal('hide');

                        }, 100);
                    });
            },
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
                    this.nombre = files[0].name;
                if(e.target.id == 'carga_layout') {
                    this.createImage(files[0]);
                }
            },
            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/index', {
                    params: {
                        sort: 'Nombre',
                        order: 'asc',
                        scope:'consolidadora',
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.cargando = false;
                    })
            },
            nombreAlias (item) {
                return `${item.nombre} - [${item.alias}]`
            },
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
