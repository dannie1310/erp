<template>
    <span>
        <button @click="cargar()" type="button" class="btn btn-outline-success pull-right" title="Cargar Layout">
            <i class="fa fa-upload"></i>Cargar Layout Excel
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> CARGAR LAYOUT DE ESTIMACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                    <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                <div class="col-lg-12">
                                    <input type="file" class="form-control" id="carga_layout"
                                            @change="onFileChange"
                                            row="3"
                                            v-validate="{ ext: ['xlsx']}"
                                            name="carga_layout"
                                            data-vv-as="Layout"
                                            ref="carga_layout"
                                            :class="{'is-invalid': errors.has('carga_layout')}">
                                    <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xlsx)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal">
                            <i class="fa fa-times-circle"></i> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" @click="validate" :disabled="!file">
                            <i class="fa fa-upload"></i> Cargar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "carga-layout-asignacion",
    components: {},
    props:['id_contrato'],
    data() {
        return {
            cargando: false,
            contratos : [],
            file: null,
            nombre: '',
        }
    },
    mounted() {
        this.$validator.reset();
        this.file = null;
    },
    methods : {
        cargar() {
            this.file = null;
            this.$validator.errors.clear();
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
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
        cargarLayout(){
            var formData = new FormData();
            formData.append('file',  this.file);
            formData.append('id',  this.id_contrato);
            formData.append('name', this.nombre);

            return this.$store.dispatch('contratos/contrato-proyectado/cargaLayout',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    this.datos_archivo = data;
                    $(this.$refs.modal_datos).appendTo('body')
                    $(this.$refs.modal_datos).modal('show');

                }).finally(() => {
                    this.$refs.carga_layout.value = '';
                    this.file = null;
                    this.file_name = '';
                    this.$validator.errors.clear();
                });
        },
        cerrarModal() {
            this.file = null;
            this.$validator.errors.clear();
            $(this.$refs.modal).modal('hide')
        },
    }


}
</script>

<style>

</style>