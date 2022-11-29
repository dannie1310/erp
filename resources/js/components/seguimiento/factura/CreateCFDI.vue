<template>
    <span>
        <button class="btn btn-app pull-right dropdown-toggle"
                type="button"
                id="dropdownMenuButton"
                data-toggle="dropdown"
                data-boundary="window"
                aria-haspopup="true"
                aria-expanded="false">
            <span><i class="fa fa-plus"></i></span>
            Registrar
        </button>
        <div class="dropdown-menu">
            <button @click="load" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Cargar" >
                <i class="fa fa-upload"></i> Cargar CFDI
            </button>
            <button @click="registrar" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Registrar" >
                <i class="fa fa-pencil"></i> Ir a Formulario
            </button>
        </div>
        <div class="modal fade" ref="modal_carga" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-carga"> <i class="fa fa-upload"></i> CARGAR FACTURA CON CFDI</h5>
                        <button type="button" class="close" v-on:click="cerrarModalCarga" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row justify-content-between">
                                        <div class="col-md-12">
                                            <div class="col-lg-12">
                                                <input type="file" class="form-control" id="file_carga"
                                                       @change="onFileChange"
                                                       row="3"
                                                       v-validate="{ ext: ['xml']}"
                                                       name="file_carga"
                                                       data-vv-as="Layout"
                                                       ref="file_carga"
                                                       :class="{'is-invalid': errors.has('file_carga')}">
                                                <div class="invalid-feedback" v-show="errors.has('file_carga')">{{ errors.first('file_carga') }} (xml)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="cerrarModalCarga" :disabled="cargando"><i class="fa fa-times"></i>Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="getLayoutData()" :disabled="errors.has('carga_layout')">
                                <i class="fa fa-spin fa-spinner" v-if="procesando"></i>
                                <i class="fa fa-upload" v-else ></i> Cargar
                            </button>

                         </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "registrar-factura-cfdi",
        props: ['cargando'],
        data() {
            return {
                procesando:false,
                file_carga : null,
                file_carga_name : null,
                data: null,
            }
        },
        mounted(){
            this.$validator.reset()
            this.procesando = false
            this.file_carga = null
            this.file_carga_name = null
            this.data = null
        },
        methods:{
            load() {
                this.file_carga_name = null;
                this.file_carga = null
                this.data = null
                this.procesando = false;

                this.$validator.errors.clear();
                this.$validator.reset();

                $(this.$refs.modal_carga).appendTo('body')
                $(this.$refs.modal_carga).modal('show');
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_carga = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            onFileChange(e){
                this.file_carga = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_carga_name = files[0].name;
                this.createImage(files[0]);
            },
            getLayoutData(){
                if(this.file_carga != null && this.file_carga_name !='') {
                    this.procesando = true;
                    var formData = new FormData();
                    formData.append('facturas', this.file_carga);
                    formData.append('nombre_archivo', this.file_carga_name);
                    return this.$store.dispatch('seguimiento/factura/cargarCFDI', {
                        data: formData, config: {params: {_method: 'POST'}}
                    }).then(data => {
                        this.$router.push({name: 'factura-seg-create', params: {datos: data}});
                        this.cerrarModalCarga();
                        }).finally(() => {
                            this.procesando = false;
                            this.cargando = false
                            this.cerrarModalCarga();
                        });
                }
                else{
                    swal('¡Error!', 'Debe seleccionar un archivo (XML).', 'error')
                }
            },
            registrar(){
                this.cerrarModalCarga();
                this.$router.push({name: 'factura-seg-create'});
            },
            cerrarModalCarga(){
                this.file_carga_name = null;
                this.file_carga = null;
                this.$refs.file_carga.value = null;
                this.$validator.errors.clear();
                this.$validator.reset();
                $(this.$refs.modal_carga).modal('hide');
            },
        },

    }
</script>
