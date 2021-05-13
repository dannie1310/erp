<template>
    <span>
         <div class="row">
             <div class="col md 12">
                 <button @click="init" class="btn btn-app pull-right" title="Cargar CSV" v-if="$root.can('registrar_proveedores_no_localizados', true)" >
                    <i class="fa fa-upload"></i>Cargar Listado
                </button>
             </div>
         </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> Cargar Listado de Proveedores No Localizados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="http://omawww.sat.gob.mx/cifras_sat/Documents/No%20localizados.csv" class="pull-right">
                                        <b>Descargar Listado</b><i class="fa fa-file-excel-o"></i>
                                    </a>
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['csv', 'zip'], size: 102400}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }}, Si el archivo es mayor al tamaño permitido deberá ser cargado como ZIP</div>
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
export default {
    name: "carga-no-localizados",
    data() {
        return {
            cargando: false,
            data: null,
            file: null,
            file_name: null,
        }
    },
    methods: {
        init(){
            this.$refs.carga_layout.value = '';
            this.file = null;
            this.$validator.errors.clear();

            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.cargar()
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
        cerrarModal(event) {
            this.$refs.carga_layout.value = '';
            this.file = null;
            this.$validator.errors.clear();
            $(this.$refs.modal).modal('hide')
        },
        cargar(){
            var formData = new FormData();
            formData.append('file',  this.file);
            formData.append('file_name',  this.file_name);
            return this.$store.dispatch('fiscal/ctg-no-localizado/cargarCsv',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    this.$emit('created', data);
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

            this.file_name = files[0].name;
            if(e.target.id == 'carga_layout') {
                this.createImage(files[0]);
            }
        },
    }

}
</script>

<style>

</style>
