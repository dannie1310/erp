<template>
    <span>
        <button type="button" class="btn btn-primary" @click="load"><i class="fa fa-upload"></i> Cargar Layout</button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-upload"></i> &nbsp; Agregar Partidas con Layout</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="col-md-12 info-box-content">
                                    <label class="col-lg-12 col-form-label">Considera el siguiente orden de columnas para cargar tu archivo CSV,  contempla que deben estar llenados los campos sombreados.
                                        &nbsp
                                        </label>
                                </div>
                                <br>
                    <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th style="background:darkgray;">No. de Parte</th>
                                                            <th>No parte Equivalente</th>
                                                            <th>Referencia</th>
                                                            <th style="background:darkgray;">Unidad</th>
                                                            <th style="background:darkgray;">Descripcíon</th>
                                                            <th style="background:darkgray;">Cantidad</th>
                                                            <th>Clave Concepto</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-8">
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               accept=".csv"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['csv']}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}">
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
    import Create from "./Create";
    export default {
        name: "carga-layout",
        components: {Create},
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
                this.data = null;
                this.$validator.errors.clear();

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
                return this.$store.dispatch('compras/requisicion/cargaLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        this.data = data;
                        this.$emit('input', this.data);
                        this.$emit('change');
                    }).finally(() => {
                        this.$refs.carga_layout.value = '';
                        this.file = null;
                        this.file_name = '';
                        this.$validator.errors.clear();
                        setTimeout(() => {
                            $(this.$refs.modal).modal('hide');
                        }, 100);
                    })

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
