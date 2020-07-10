<template>
    <span>
         <button @click="open" type="button" class="btn btn-app btn-secondary float-right" title="Cargar">
              <i class="fa fa-folder-plus"></i> Directorio
         </button>
        <form role="form" @submit.prevent="procesar">
        <div class="modal fade" ref="modal_procesa_directorio" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i>Procesar Directorio</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <span v-if="procesado">
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
                            <button type="submit" class="btn btn-primary" v-if="!procesado" :disabled="procesando">
                                <span v-if="procesando">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </span>
                                <span v-else>
                                    <i class="fa fa-redo-alt"></i> Procesar
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
        name: "cfd-sat-procesa-directorio",
        data() {
            return {
                procesado : false,
                procesando : false,
                resultado : [],
            }
        },
        mounted(){

        },

        methods: {
            procesar(){
                this.procesando = true;
                return this.$store.dispatch('seguridad/fiscal/cfd-sat/procesaDirZIPCFD',
                {
                    data: [],
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    this.resultado = data;
                    this.procesado = true;
                }).finally(() => {
                    this.procesando = false;
                });
            },
            open(){
                $(this.$refs.modal_procesa_directorio).modal('show');
            },
            close(){
                this.resultado = [];
                this.procesado = false;
                $(this.$refs.modal_procesa_directorio).modal('hide');
            }
        }
    }
</script>
