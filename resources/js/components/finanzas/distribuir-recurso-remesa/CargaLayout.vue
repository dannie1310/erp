<template>
    <span>
        <button @click="load" class="btn btn-sm btn-outline-info" title="Cargar" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-upload" v-else></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Selecciona archivo de layout.</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                        <label for="layout_interbancario" class="col-lg-12 col-form-label">Layout Interbancario</label>
                            <div class="col-lg-12">
                                <input type="file" class="form-control" id="carga_layout_interbancario" @change="onFileChange"
                                row="3"
                                v-validate="{ ext: ['doc']}"
                                name="carga_layout_interbancario"
                                data-vv-as="Layout Interbancario"
                                ref="carga_layout_interbancario"
                                :class="{'is-invalid': errors.has('carga_layout_interbancario')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('carga_layout_interbancario')">{{ errors.first('carga_layout_interbancario') }} (doc)</div>
                            </div>
                        </div>

                        <div class="form-group row">
                        <label for="layout" class="col-lg-12 col-form-label">Layout mismo banco</label>
                            <div class="col-lg-12">
                                <input type="file" class="form-control" id="carga_layout_mismo_banco" @change="onFileChange"
                                       row="3"
                                       v-validate="{ ext: ['csv']}"
                                       name="carga_layout_mismo_banco"
                                       data-vv-as="Layout"
                                       ref="carga_layout_mismo_banco"
                                       :class="{'is-invalid': errors.has('carga_layout_mismo_banco')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('carga_layout_mismo_banco')">{{ errors.first('carga_layout_mismo_banco') }} (csv)</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="validate">Cargar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "distribucion-carga-layout",
        props: ['id'],
        data() {
            return {
                cuenta : null,
                cargando : false,
                file_mismo_banco : null,
                file_interbancario : null
            }
        },

        // mounted() {
        //     $(this.$refs.modal).on('hide.bs.modal', () => {
        //         $('.modal-backdrop fade show').remove();
        //     });
        // },

        methods: {
            load() {
                $(this.$refs.modal).modal('show')
                this.$refs.carga_layout_interbancario.value = '';
                this.$refs.carga_layout_mismo_banco.value = '';
                this.file_interbancario = null;
                this.file_mismo_banco = null;
            },
            cerrarModal(event) {
                this.$refs.carga_layout_interbancario.value = '';
                this.$refs.carga_layout_mismo_banco.value = '';
                this.file_interbancario = null;
                this.file_mismo_banco = null;

                this.$validator.errors.clear();
                $(this.$refs.modal).modal('hide')
            },
            cargarLayout(){
                var formData = new FormData();
                formData.append('file_mismo_banco',  this.file_mismo_banco);
                formData.append('file_interbancario',  this.file_interbancario);
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/cargaManualLayout',
                    {
                        id: this.id,
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(() => {
                        this.$emit('success')
                    }).finally(() => {
                        setTimeout(() => {
                            $(this.$refs.modal).modal('hide');

                        }, 100);
                    });
            },
            onFileChange(e){
                this.file_interbancario = null;
                this.file_mismo_banco = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                if(e.target.id == 'carga_layout_interbancario') {
                    this.createImage(files[0], 1);
                }else{
                    this.createImage(files[0], 2);
                }
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    if(tipo == 1) {
                        vm.file_interbancario = e.target.result;
                    }else{
                        vm.file_mismo_banco = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        this.cargarLayout()
                    }else{
                        if(this.$refs.carga_layout_interbancario.value !== ''){
                            this.$refs.carga_layout_interbancario.value = '';
                            this.file_interbancario = null;
                        }
                        if(this.$refs.carga_layout_mismo_banco.value !== ''){
                            this.$refs.carga_layout_mismo_banco.value = '';
                            this.file_mismo_banco = null;
                        }

                        this.$validator.errors.clear();
                        swal('¡Error!', 'Error archivos de entrada invalidos.', 'error')
                    }
                });
            },
        }
    }
</script>

<style scoped>

</style>
