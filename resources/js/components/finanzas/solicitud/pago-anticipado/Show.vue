<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalles de Solicitud de Pago Anticipado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table v-if="pagoAnticipado" class="table">
                                <tbody>
                                <tr>
                                    <th style="text-align: right">Número de Folio</th>
                                    <td>{{ pagoAnticipado.numero_folio}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Transacción Antecedente</th>
                                    <td>{{ pagoAnticipado.antecedente}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Monto</th>
                                    <td>{{ pagoAnticipado.monto_format}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Beneficiario</th>
                                    <td>{{ pagoAnticipado.beneficiario}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Fecha y Hora de Registro</th>
                                    <td>{{ pagoAnticipado.fecha_format}}</td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="form-group" v-if="pagoAnticipado">
                                <label>Observaciones</label>
                                <p>{{ pagoAnticipado.observaciones }}</p>
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
        name: "solicitud-pago-anticipado-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/solicitud-pago-anticipado/SET_SOLICITUD', null);
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/find', {
                    id: id,
                    params: { include: 'cuenta.empresa,transaccion' }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-pago-anticipado/SET_SOLICITUD', data);
                    $(this.$refs.modal).modal('show')
                })
            }
        },
        computed: {
            pagoAnticipado() {
                return this.$store.getters['finanzas/solicitud-pago-anticipado/currentSolicitud']
            }
        }
    }
</script>

<style scoped>

</style>