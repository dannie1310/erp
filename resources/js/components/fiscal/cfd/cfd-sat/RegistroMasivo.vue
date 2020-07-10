<template>
    <span>
         <button @click="open" type="button" class="btn btn-app btn-secondary float-right" title="Cargar">
              <i class="fa fa-upload"></i> Cargar
         </button>
        <form role="form" @submit.prevent="validate">
        <div class="modal fade" ref="modal_carga_masiva" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i>Carga Masiva de CFD</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <span v-if="!procesado">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label class="col-form-label"><i class="fa fa-file-archive"></i> Archivo ZIP:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <input type="file" class="form-control" id="carga_zip"
                                                   @change="onFileChange"
                                                   row="3"
                                                   v-validate="{ ext: ['zip']}"
                                                   name="carga_zip"
                                                   data-vv-as="zip"
                                                   ref="carga_zip"
                                                   :class="{'is-invalid': errors.has('carga_zip')}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has('carga_zip')">{{ errors.first('carga_zip') }} (zip)</div>
                                        </div>
                                    </div>
                                </div>
                            </span>

                            <span v-else>
                                <h6><i class="fa fa-arrow-circle-right"></i><b>Resultado del procesamiento</b></h6>
                                <div class="table-responsive">
                                    <table style="width: 100%" class="table table-stripped">
                                        <tbody>
                                            <tr>
                                                <th style="text-align: left" >Duración de procesamiento (segundos):</th>
                                                <td style="text-align: right">{{resultado.duracion}}</td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: left" >Nombre de archivo zip:</th>
                                                <td style="text-align: right">{{resultado.nombre_archivo_zip}}</td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: left" >Núm. archivos leidos:</th>
                                                <td style="text-align: right">{{resultado.archivos_leidos}}</td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: left" >Núm. archivos cargados:</th>
                                                <td style="text-align: right">{{resultado.archivos_cargados}}</td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: left" >Núm. proveedores cargados:</th>
                                                <td style="text-align: right">{{resultado.proveedores_nuevos}}</td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: left" >Núm. archivos no cargados:</th>
                                                <td style="text-align: right">{{resultado.archivos_no_cargados}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left" >-Núm. archivos preexistentes:</td>
                                                <td style="text-align: right">{{resultado.archivos_preexistentes}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left" >-Núm. archivos receptor no válido:</td>
                                                <td style="text-align: right">{{resultado.archivos_receptor_no_valido}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left" >-Núm. archivos error app:</td>
                                                <td style="text-align: right">{{resultado.archivos_no_cargados_error_app}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                               </div>
                            </span>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" v-if="!procesado" :disabled="cargando || errors.count() > 0">
                                <span v-if="cargando">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </span>
                                <span v-else>
                                    <i class="fa fa-upload"></i> Cargar
                                </span>
                            </button>
                            <button type="button" class="btn btn-secondary" @click="close">Cerrar</button>
                        </div>
                    </div>

            </div>
        </div>
        </form>
    </span>
</template>
<script>
    export default {
        name: "cfd-sat-registro-masivo",
        data() {
            return {
                cargando: false,
                procesado : false,
                archivo : '',
                file_zip : null,
                file_zip_name : '',
                resultado : [],
            }
        },
        mounted(){

        },

        methods: {

            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_zip = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            onFileChange(e){
                this.file_zip = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_zip_name = files[0].name;
                this.createImage(files[0]);
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.$refs.carga_zip.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning');
                        }else{
                            this.cargarZIP()
                        }
                    }else{
                        if(this.$refs.carga_zip.value !== ''){
                            this.$refs.carga_zip.value = '';
                            this.file_zip = null;
                        }
                        this.$validator.errors.clear();
                    }
                });
            },
            cargarZIP(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('archivo_zip',  this.file_zip);
                formData.append('nombre_archivo',  this.file_zip_name);
                return this.$store.dispatch('seguridad/fiscal/cfd-sat/cargarZIP',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    this.resultado = data;
                    this.$refs.carga_zip.value = '';
                    this.procesado = true;
                }).finally(() => {
                    this.cargando = false;
                });
            },
            open(){
                $(this.$refs.modal_carga_masiva).modal('show');
            },
            close(){
                this.file_zip = null;
                this.resultado = [];
                this.procesado = false;
                $(this.$refs.modal_carga_masiva).modal('hide');
            }
        }
    }
</script>
