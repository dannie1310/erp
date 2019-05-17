<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div ref="modal" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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

                                    <th style="text-align: right">Beneficiario</th>
                                    <td v-if="pagoAnticipado.empresa">{{ pagoAnticipado.empresa.razon_social}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Monto</th>
                                    <td>{{ pagoAnticipado.monto_format}}</td>

                                    <th style="text-align: right">Fecha y Hora de Registro</th>
                                    <td>{{ pagoAnticipado.fecha_format}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Registro</th>
                                    <td v-if="pagoAnticipado.usuario">{{ pagoAnticipado.usuario.nombre}}</td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="form-group" v-if="pagoAnticipado">
                                <label>Observaciones</label>
                                <p>{{ pagoAnticipado.observaciones }}</p>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" v-if="pagoAnticipado">
                        <table class="table">
                            <h5 class="modal-title">Transacción Antecedente</h5>
                            <h6 align="left" v-if="pagoAnticipado.subcontrato">Fecha: {{ pagoAnticipado.subcontrato.fecha_format}}</h6>
                            <h6 align="left" v-if="pagoAnticipado.orden_compra">Fecha: {{ pagoAnticipado.orden_compra.fecha_format}}</h6>
                            <tbody>
                                <tr>
                                    <th style="text-align: right">Número de Folio</th>
                                    <td v-if="pagoAnticipado.subcontrato">{{ pagoAnticipado.subcontrato.numero_folio_format}}</td>
                                    <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.numero_folio_format}}</td>
                                    <th style="text-align: right">Tipo de Transacción</th>
                                    <td v-if="pagoAnticipado.subcontrato">{{ pagoAnticipado.subcontrato.tipo_nombre}}</td>
                                    <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.tipo_nombre}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Observaciones</th>
                                    <td v-if="pagoAnticipado.subcontrato">{{ pagoAnticipado.subcontrato.observaciones}}</td>
                                    <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.observaciones}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Subtotal:</th>
                                    <td v-if="pagoAnticipado.subcontrato">{{ pagoAnticipado.subcontrato.subtotal_format}}</td>
                                    <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.subtotal_format}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">IVA:</th>
                                    <td v-if="pagoAnticipado.subcontrato">{{ pagoAnticipado.subcontrato.impuesto_format}}</td>
                                    <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.impuesto_format}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Total:</th>
                                    <td v-if="pagoAnticipado.subcontrato">{{ pagoAnticipado.subcontrato.total_format}}</td>
                                    <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.total_format}}</td>
                                </tr>

                            </tbody>
                        </table>
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
                    params: { include: ['subcontrato','empresa','usuario','orden_compra'] }
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