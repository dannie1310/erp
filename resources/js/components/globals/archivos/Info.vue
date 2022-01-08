<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Información">
            <i class="fa fa-info-circle"></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-info-circle"></i> Información de Archivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="card" v-if="!archivo">
                            <div class="card-body">
                                <div class="row" >
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                           <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-else>


                            <table class="table table-sm table-bordered">
                                <tr>
                                    <td colspan="2" style="background-color: #ddd">
                                       {{archivo.nombre}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="c100">
                                        Fecha de Registro:
                                    </td>
                                    <td>
                                        <b>{{ archivo.fecha_registro_format }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Registró:
                                    </td>
                                    <td>
                                        <b>{{ archivo.registro }}</b>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Tipo de Archivo:
                                    </td>
                                    <td>
                                        <b>{{ archivo.tipo_archivo_txt }}</b>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Observaciones:
                                    </td>
                                    <td>
                                        <b>{{ archivo.observaciones }}</b>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>


    export default {
        name: "ShowInfoArchivoIvitacion",
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },

        methods: {
            find() {
                this.$store.commit('documentacion/archivo/SET_ARCHIVO', null);
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('documentacion/archivo/getArchivoInvitacion', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('documentacion/archivo/SET_ARCHIVO', data);
                }).finally(() => {
                    this.cargando = false;
                })
            }
        },
        computed: {
            archivo() {
                return this.$store.getters['documentacion/archivo/currentArchivo']
            },
        }
    }
</script>

<style scoped>

table{
    font-size: 11px;
}

</style>
