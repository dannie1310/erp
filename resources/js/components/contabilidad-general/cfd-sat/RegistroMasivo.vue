<template>
    <span>
         <button @click="open" type="button" class="btn btn-app btn-secondary float-right" title="Cargar">
              <i class="fa fa-upload"></i> Cargar
         </button>
        <form role="form" @submit.prevent="validate">
        <div class="modal fade" ref="modal_carga_masiva" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i>Carga Masiva de CFD</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                 <div class="col-md-12">
                                     <div class="form-group row error-content">
                                         <label for="id_empresa" class="col-md-2 col-form-label">Empresa:</label>
                                         <div class="col-md-10">
                                             <model-list-select
                                                     :disabled="cargando"
                                                     :onchange="changeSelect()"
                                                     name="id_empresa"
                                                     v-model="id_empresa"
                                                     v-validate="{required: true}"
                                                     option-value="id"
                                                     :custom-text="rfcAndRazonSocial"
                                                     :list="empresas"
                                                     :placeholder="!cargando?'Seleccionar o buscar empresa por razón social o rfc':'Cargando...'"
                                                     :isError="errors.has('id_empresa')">
                                             </model-list-select>
                                         </div>
                                     </div>
                                 </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label class="col-md-2 col-form-label">Archivo ZIP:</label>
                                        <div class="col-md-10">
                                            <input type="file" class="form-control" id="carga_zip"
                                                   @change="onFileChange"
                                                   row="3"
                                                   v-validate="{ ext: ['zip']}"
                                                   name="carga_zip"
                                                   data-vv-as="zip"
                                                   ref="carga_zip"
                                                   :class="{'is-invalid': errors.has('carga_zip')}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has('carga_zip')">{{ errors.first('carga_zip') }} (zip)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0">
                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                <i class="fa fa-upload" v-else></i> Cargar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>

            </div>
        </div>
        </form>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "cfd-sat-registro-masivo",
        components: {ModelListSelect},

        data() {
            return {
                cargando: false,
                id_empresa: '',
                empresas: [],
                empresa_seleccionada: [],
                archivo : '',
                file_zip : null,
                file_zip_name : '',
            }
        },
        mounted(){
            this.getEmpresas();
        },

        methods: {
            changeSelect(){
                this.conectando = false;
                var busqueda = this.empresas.find(x=>x.id === this.id_empresa);
                if(busqueda != undefined)
                {
                    this.empresa_seleccionada = busqueda;
                }
            },

            rfcAndRazonSocial (item){
                return `[${item.rfc}] - ${item.razon_social}`
            },

            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa-sat/index', {
                    params: {
                        sort: 'razon_social',
                        order: 'asc',
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.cargando = false;
                    })
            },

            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_zip = e.target.result;
                };
                reader.readAsDataURL(file);

            },

            onFileChange(e){
                this.file_zip = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_zip_name = files[0].name;
                this.createImage(files[0]);
                /*setTimeout(() => {
                    this.validate()
                }, 500);*/
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.$refs.carga_zip.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning');
                        }else{
                            this.cargarZIP()
                        }
                    }else{
                        if(this.$refs.carga_zip.value !== ''){
                            this.$refs.carga_zip.value = '';
                            this.file_zip = null;
                        }
                        this.$validator.errors.clear();
                    }
                });
            },
            cargarZIP(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('archivo_zip',  this.file_zip);
                formData.append('nombre_archivo',  this.file_zip_name);
                formData.append('id_empresa',  this.id_empresa);
                return this.$store.dispatch('contabilidadGeneral/cfd-sat/cargarZIP',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    /*if(data.data.length > 0){
                        this.pagos = data.data;
                        this.cuentas_cargo = data.cuentas_cargo;
                        this.fechas_validacion = data.fechas_validacion;
                        this.resumen = data.resumen;
                        this.fechasDeshabilitadas.from=new Date(this.fechas_validacion.from);
                        this.fechasDeshabilitadas.to=new Date(this.fechas_validacion.to);
                        this.id_moneda_obra = data.id_moneda_obra;

                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file_pagos = null;
                        }
                        this.pagos = [];
                        swal('Carga Masiva', 'Archivo de layout sin pagos válidos.', 'warning')
                    }*/
                }).finally(() => {
                    this.cargando = false;
                });
            },
            open(){
                $(this.$refs.modal_carga_masiva).modal('show');
            },
        }
    }
</script>