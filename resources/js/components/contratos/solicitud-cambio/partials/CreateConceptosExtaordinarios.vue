<template>
    <span>
        <button class="btn btn-primary" title="Agregar conceptos extraordinarios por Layout" @click="init()">
            <i class="fa fa-file-excel"></i> Agregar Conceptos Extraordinarios
        </button>

        <div class="modal fade" ref="modalExtraordinario" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-file-excel"></i> Conceptos Extraordinarios por Layout</h5>
                        <button type="button" class="close" @click="cerrar()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label class="col-form-label" for="contrato">Seleccione el nodo al que cargará los nuevos conceptos extraordinarios:</label>
                                        <select-contrato
                                            name="contrato"
                                            v-validate="{required: true}"
                                            data-vv-as="Nodo para carga"
                                            id="contrato"
                                            v-model="nodo_carga"
                                            :error="errors.has('contrato')"
                                            :idContratoProyectado = this.id_contrato_proyectado
                                            ref="nodoContratoSelect"
                                            :disableBranchNodes="true"
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
                                               name="layout"
                                               data-vv-as="Layout"
                                               ref="cargar_file"
                                               :class="{'is-invalid': errors.has('cargar_file')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} <span>(.xlsx)</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrar()">
                                <i class="fa fa-close"  ></i>
                                Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-retweet"></i>
                                Procesar
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
name: "CreateConceptosExtaordinarios",
    props: ['id_contrato_proyectado'],
    data(){
        return {
            nodo_carga:'',
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
                    this.$emit("agrega-extraordinario",this.concepto);
                    $(this.$refs.modalExtraordinario).modal('hide');
                    this.$validator.reset()
                }
            });
        },
        createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;
            reader.onload = (e) => {
                vm.files[tipo] = {archivo:e.target.result}
            };
            reader.readAsDataURL(file);
        },
        onFileChange(e){
            var size = 0;
            this.files = [];
            this.names = [];
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            if(e.target.id == 'cargar_file') {
                for(let i=0; i<files.length; i++) {
                    this.createImage(files[i]);
                    size = +size + +files[i].size;
                    this.names[i] = {
                        nombre: files[i].name,
                    };
                    this.createImage(files[i], i);
                }
            }
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
                    if(this.$refs.cargar_file !== undefined){
                        this.$refs.cargar_file.value = '';
                    }
                    this.names = [];
                    this.files = [];
                    $(this.$refs.modal).modal('hide');
                })
            }

        },
        validarExtensiones(){
            return ['xlsx'/*, 'jpg', 'jpeg', 'png'*/];
        },
    }
}
</script>

<style scoped>

</style>
