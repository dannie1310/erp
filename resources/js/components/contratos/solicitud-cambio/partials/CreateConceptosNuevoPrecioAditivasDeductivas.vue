<template>
    <span>
        <button class="btn btn-primary" title="Agregar conceptos extraordinarios por Layout" @click="init()">
            <i class="fa fa-file-excel"></i> Cambios de Precio y Volúmen
        </button>

        <div class="modal fade" ref="modalExtraordinario" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-file-excel"></i> Cambios de Precio y Volúmen por Layout</h5>
                        <button type="button" class="close" @click="cerrar()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row" v-if="tiene_nodo_cambio_precio">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label class="col-form-label" for="contrato">Seleccione el nodo que será padre de los nuevos conceptos con cambio de precio:</label>
                                        <select-contrato
                                            name="contrato"
                                            v-validate="{required: true}"
                                            data-vv-as="Nodo para carga"
                                            id="contrato"
                                            v-model="nodo_carga"
                                            :error="errors.has('contrato')"
                                            :idContratoProyectado = this.id_contrato_proyectado
                                            ref="nodoContratoSelect"
                                            :disableBranchNodes="false"
                                        ></select-contrato>

                                        <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} <span>(.xlsx)</span></div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label class="col-form-label">Seleccione el Archivo del Layout:</label>
                                        <input type="file" class="form-control" id="cargar_file"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{required:true, ext: validarExtensiones(),  size: 102400}"
                                               name="file_carga"
                                               data-vv-as="Layout"
                                               ref="file_carga"
                                               :class="{'is-invalid': errors.has('cargar_file')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('file_carga')">{{ errors.first('file_carga') }} <span>(.xlsx)</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" :disabled="cargando" @click="descargar()">
                                <i class="fa fa-download" v-if="!cargando"></i>
                                <i class="fa fa-spinner fa-spin" v-else></i>
                                Descargar
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="cargando">
                                <i class="fa fa-retweet" v-if="!cargando"></i>
                                <i class="fa fa-spinner fa-spin" v-else></i>
                                Procesar
                            </button>
                            <button type="button" class="btn btn-secondary" @click="cerrar()">
                                <i class="fa fa-close"  ></i>
                                Cerrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import SelectContrato from "../../proyectado/partials/SelectContrato";
export default {
name: "CreateConceptosNuevoPrecioAditivasDeductivas",
    props: ['id_contrato_proyectado','tiene_nodo_cambio_precio','id_subcontrato', 'cantidad_conceptos'],
    data(){
        return {
            nodo_carga:'',
            file_carga : null,
            file_carga_name : '',
            partidas : '',
            cargando : false,
        }
    },
    components: {SelectContrato},
    mounted() {

    },
    methods:{
        init() {
            $(this.$refs.modalExtraordinario).modal('show');
            this.$validator.reset();
        },
        cerrar() {
            $(this.$refs.modalExtraordinario).modal('hide');
            this.$validator.reset();
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.procesaLayout();
                }
            });
        },
        descargar() {
            if(this.cantidad_conceptos>0){
                this.cargando = true;
                return this.$store.dispatch('contratos/subcontrato/descargarLayoutCambiosPrecioVolumen', {id: this.id_subcontrato})
                    .then(() => {
                        this.$emit('success')
                        this.cargando = false;
                    })
            }else {
                swal("Error", "Este subcontrato no tiene partidas disponibles para modificar precio o volumen.","error");
            }


        },
        createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;
            reader.onload = (e) => {
                vm.file_carga = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        onFileChange(e){
            var size = 0;
            this.file_carga = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.file_carga_name = files[0].name;
            this.createImage(files[0]);

            size = files[0].size;

            if(size > 102400000){
                swal("El tamaño máximo permitido para la carga de archivos es de 100 MB.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: 'Enterado',
                            closeModal: true,
                        }
                    }
                }) .then(() => {
                    if(this.$refs.file_carga !== undefined){
                        this.$refs.file_carga.value = '';
                    }
                    this.file_carga_name = null;
                    this.file_carga = null;
                    $(this.$refs.modal).modal('hide');
                })
            }

        },
        validarExtensiones(){
            return ['xlsx'/*, 'jpg', 'jpeg', 'png'*/];
        },
        procesaLayout(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('id_contrato_proyectado',  this.id_contrato_proyectado);
            formData.append('extraordinarios',  this.file_carga);
            formData.append('nombre_archivo',  this.file_carga_name);
            formData.append('id_contrato_nodo_carga',  this.nodo_carga);
            return this.$store.dispatch('contratos/solicitud-cambio/procesarLayoutExtraordinarios',{
                data: formData, config: { params: { _method: 'POST'}}
            })
            .then(data => {
                this.partidas = data;
            }).finally(() => {
                this.cargando = false;
                this.$emit("agrega-extraordinarios",this.partidas);
                $(this.$refs.modalExtraordinario).modal('hide');
                this.$validator.reset();
            });
        }
    }
}
</script>

<style scoped>

</style>
