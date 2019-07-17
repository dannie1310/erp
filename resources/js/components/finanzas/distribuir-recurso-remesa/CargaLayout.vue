<template>
    <span>
        <button @click="load" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-upload" v-else></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Seleccionar archivo de layout.</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
<!--                        <label for="layout" class="col-lg-2 col-form-label"></label>-->
                            <div class="col-lg-12">
                                <input type="file" class="form-control" id="carga_layout" @change="onFileChange"
                                row="3"
                                v-validate="{ ext: ['csv', 'txt']}"
                                name="carga_layout"
                                data-vv-as="Layout"
                                ref="carga_layout"
                                :class="{'is-invalid': errors.has('carga_layout')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (csv, txt)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="cargarLayout">Cargar</button>
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
                cuenta: null,
                cargando: false,
                file:null
            }
        },
        methods: {
            load() {
                $(this.$refs.modal).modal('show')
            },
            cerrarModal(event) {
                console.log(this.$refs.carga_layout.value);
                this.$refs.carga_layout.value = '';
                this.$validator.errors.clear();
                $(this.$refs.modal).modal('hide')
            },
            cargarLayout(e){
                var formData = new FormData();
                formData.append('file',  this.file);
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
                    })
            },
            onFileChange(e){
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                //var image = new Image();
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
</script>

<style scoped>

</style>
