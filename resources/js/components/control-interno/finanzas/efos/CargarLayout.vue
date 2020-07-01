<template>
    <span>
        <button @click="load" class="btn btn-app btn-info float-right" title="Cargar Layout" :disabled="cargando" v-if="$root.can('actualizar_efos', true) || true">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-upload" v-else></i>
            Carga de EFOS
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-upload"></i> Actualizar lista de EFOS registrados en el SAT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-8 info-box-content">
                                    <label class="col-lg-12 col-form-label">Descargar listado completo de contribuyentes desde el portal del SAT.
                                        &nbsp
                                        <a href="http://omawww.sat.gob.mx/cifras_sat/Documents/Listado_Completo_69-B.csv">
                                            <span class="info-box-text">Descargar
                                                <i class="fa fa-file-excel-o"></i> </span></a></p></a></label>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col-md-8">
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['csv']}"
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

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="validate" :disabled="!file">Cargar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </span>
</template>

<script>
    export default {
        name: "cargar-layout",
        props: ['id'],
        data() {
            return {
                cargando: false,
                data: null,
                file: null,
            }
        },
        mounted(){
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
                $(this.$refs.modal).modal('hide')
            },
            cargarLayout(){
                var formData = new FormData();
                formData.append('file',  this.file);
                return this.$store.dispatch('seguridad/finanzas/ctg-efos/cargaLayoutEfos',
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
                if(e.target.id == 'carga_layout') {
                    this.createImage(files[0]);
                }
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
