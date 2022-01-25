<template>
    <span>
        <button @click="load" v-if="$root.can('registrar_contrato_proyectado')" class="btn btn-app pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i> Registrar
        </button>
        <div class="modal fade" ref="modal_carga" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-carga"> <i class="fa fa-file-excel-o"></i> Seleccionar Archivo de Layout</h5>
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
                                                <input type="file" class="form-control" id="carga_layout"
                                                    @change="onFileChange"
                                                    row="3"
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
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="cerrarModalCarga" :disabled="cargando"><i class="fa fa-times"></i>Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="getLayoutData()" :disabled="errors.has('carga_layout') || file_carga === null">
                                <i class="fa fa-spin fa-spinner" v-if="procesando"></i>
                                <i class="fa fa-upload" v-else ></i> Cargar
                            </button>
                            <button @click="registrar" type="button" class="btn btn-sm btn-outline-info" title="Editar" >
                                <i class="fa fa-pencil"></i>Ir a Formulario
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
    name: "seleccion-tipo-registro",
    props: ['cargando'],
    data() {
        return {
            procesando:false,
            file_carga : null,
            file_carga_name : '',
        }
    },
    mounted(){
    },
    methods:{
        load() {
            this.file = null;
            this.$validator.errors.clear();

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
            this.procesando = true;
            var formData = new FormData();
            formData.append('pagos',  this.file_carga);
            formData.append('nombre_archivo',  this.file_carga_name);
            return this.$store.dispatch('contratos/contrato-proyectado/cargarLayout',{
                    data: formData, config: { params: { _method: 'POST'}}
            })
            .then(data => {
                if(data.partidas_con_error){

                }else{
                    this.$router.push({name: 'proyectado-layout-create', params: {partidas:data.contratos}});
                }
            }).finally(() => {
                this.procesando = false;
                this.cerrarModalCarga();
            });
        },
        registrar(){
            this.cerrarModalCarga();
            this.$router.push({name: 'proyectado-create'});
        },
        cerrarModalCarga(){
            if(this.$refs.carga_layout.value !== ''){
                this.$refs.carga_layout.value = '';
                this.file_carga = null;
            }
            $(this.$refs.modal_carga).modal('hide');
            this.$validator.reset();
        },
    },

}
</script>

<style>

</style>