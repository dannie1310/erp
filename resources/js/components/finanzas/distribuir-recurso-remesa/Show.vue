<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-list-alt"></i> DISTRIBUCIÓN DE RECURSOS AUTORIZADOS DE LA REMESA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fa fa-list"></i> Información de Distribución de Remesa Liberada
                        </h4>
                    </div>
                </div>
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td class="bg-gray-light"><b>Folio Distribución:</b><br>GDG
                                        </td>
                                        <td class="bg-gray-light"><b>Fecha de Registro:</b><br>FDGDFG
                                        </td>
                                        <td class="bg-gray-light"><b>Monto Total de Remesa:</b><br>DFGDFG
                                        </td>
                                        <td class="bg-gray-light"><b>Monto de Está Distribución:</b><br>$DFGDFG
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-gray-light">
                                            <b>Usuario de Registro:</b>
                                            <br>
                                            DFGD
                                        </td>
                                        <td colspan="2" class="bg-gray-light"><b>Estado:</b><br>
                                            DFGDFG
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-gray-light"><b>Usuario de Cancelación</b><br>
                                            FFFFF
                                        </td>
                                         <td colspan="2" class="bg-gray-light">
                                            <b>Usuario de Registro:</b>
                                            <br>
                                            DFGD
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="bg-gray-light">
                                            <b>Concepto:</b><br>
                                           gdfgdfg
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>

                    </div>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
    export default {
        name: "distribuir-recurso-remesa-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', null);
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/find', {
                    id: id,
                    params: { include: 'remesa_liberada.remesa.documento' }
                }).then(data => {
                    this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', data);
                    $(this.$refs.modal).modal('show')
                })
            }
        },
        computed: {
            distribucion() {
                return this.$store.getters['finanzas/distribuir-recurso-remesa/currentDistribucion']
            },
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
        }
    }
</script>

<style scoped>

</style>