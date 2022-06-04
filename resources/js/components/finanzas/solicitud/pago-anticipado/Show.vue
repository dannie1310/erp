<template>
    <span>
        <div class="invoice p-3 mb-3">
            <div  v-if="!pagoAnticipado">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-else>
                <div class="table-responsive col-12">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th style="text-align: left">Fecha de Solicitud:</th>
                            <td>{{pagoAnticipado.cumplimiento}}</td>
                            <th style="text-align: left">Fecha Limite de Pago:</th>
                            <td>{{ pagoAnticipado.vencimiento}}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left" v-if="pagoAnticipado.empresa">Empresa:</th>
                            <td v-if="pagoAnticipado.empresa">{{ pagoAnticipado.empresa.razon_social}}</td>
                            <th style="text-align: left" v-if="pagoAnticipado.orden_compra || pagoAnticipado.subcontrato">Referencia:</th>
                            <td v-if="pagoAnticipado.subcontrato">{{pagoAnticipado.subcontrato.referencia}}</td>
                            <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.referencia}}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left" v-if="pagoAnticipado.subcontrato">Tipo de Transacción:</th>
                            <td v-if="pagoAnticipado.subcontrato">{{pagoAnticipado.subcontrato.tipo_nombre}}</td>
                            <th style="text-align: left" v-if="pagoAnticipado.orden_compra">Tipo de Transacción:</th>
                            <td v-if="pagoAnticipado.orden_compra">{{pagoAnticipado.orden_compra.tipo_nombre}}</td>
                            <th style="text-align: left" v-if="pagoAnticipado.subcontrato">Transacción:</th>
                            <td v-if="pagoAnticipado.subcontrato">({{pagoAnticipado.subcontrato.tipo_nombre}}){{pagoAnticipado.subcontrato.numero_folio_format}}({{pagoAnticipado.subcontrato.referencia}})</td>
                            <th style="text-align: left"  v-if="pagoAnticipado.orden_compra">Transacción:</th>
                            <td  v-if="pagoAnticipado.orden_compra">({{pagoAnticipado.orden_compra.tipo_nombre}}){{pagoAnticipado.orden_compra.numero_folio_format}}({{pagoAnticipado.orden_compra.referencia}})</td>
                        </tr>
                        <tr>
                            <th style="text-align: left" v-if="pagoAnticipado.costo">Costos:</th>
                            <td v-if="pagoAnticipado.costo">{{pagoAnticipado.costo.descripcion}}</td>
                            <th style="text-align: left">Estado:</th>
                            <td v-if="pagoAnticipado.estado === 0">Registrada</td>
                            <td v-if="pagoAnticipado.estado === 2 || pagoAnticipado.estado === -2 ">Cancelada</td>
                        </tr>
                        <tr>
                            <th style="text-align: left">Fecha y Hora de Registro:</th>
                            <td>{{pagoAnticipado.fecha_format}}</td>
                            <th style="text-align: left" v-if="pagoAnticipado.usuario">Registro:</th>
                            <td v-if="pagoAnticipado.usuario">{{ pagoAnticipado.usuario.nombre}}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left">Motivo:</th>
                            <td>{{pagoAnticipado.observaciones}}</td>
                            <th></th>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" align="right"  v-if="pagoAnticipado">
            <!--<div class="row" align="right"  v-if="pagoAnticipado && (pagoAnticipado.subcontrato || pagoAnticipado.orden_compra)">-->
                <div class="table-responsive col-md-12">
                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr class="bg-white">
                                        <th style="text-align: left">Monto Solicitado:</th>
                                        <td style="text-align: right">{{ pagoAnticipado.monto_format}}</td>
                                    </tr>
                                    <!--<tr v-if="pagoAnticipado.subcontrato">-->
                                    <!--<th style="width:50%" class="bg-gray-light">Subtotal:</th>-->
                                    <!--<td class="bg-gray-light" align="right">{{pagoAnticipado.subcontrato.subtotal_format}}</td>-->
                                    <!--</tr>-->
                                    <!--<tr  v-if="pagoAnticipado.orden_compra">-->
                                    <!--<th style="width:50%" class="bg-gray-light">Subtotal:</th>-->
                                    <!--<td class="bg-gray-light" align="right">{{pagoAnticipado.orden_compra.subtotal_format}}</td>-->
                                    <!--</tr>-->
                                    <!--<tr v-if="pagoAnticipado.subcontrato">-->
                                    <!--<th>IVA:</th>-->
                                    <!--<td align="right">{{pagoAnticipado.subcontrato.impuesto_format}}</td>-->
                                    <!--</tr>-->
                                    <!--<tr  v-if="pagoAnticipado.orden_compra">-->
                                    <!--<th>IVA:</th>-->
                                    <!--<td align="right">{{pagoAnticipado.orden_compra.impuesto_format}}</td>-->
                                    <!--</tr>-->
                                    <!--<tr v-if="pagoAnticipado.subcontrato">-->
                                    <!--<th class="bg-gray-light">Total:</th>-->
                                    <!--<td class="bg-gray-light" align="right">{{pagoAnticipado.subcontrato.monto_format}}</td>-->
                                    <!--</tr>-->
                                    <!--<tr  v-if="pagoAnticipado.orden_compra">-->
                                    <!--<th class="bg-gray-light">Total:</th>-->
                                    <!--<td class="bg-gray-light" align="right">{{pagoAnticipado.orden_compra.monto_format}}</td>-->
                                    <!--</tr>-->
                                </tbody>
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
        name: "solicitud-pago-anticipado-show",
        mounted() {
            this.find();
        },
        props: ['id'],
        methods: {
            find() {
                this.$store.commit('finanzas/solicitud-pago-anticipado/SET_SOLICITUD', null);
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/find', {
                    id: this.id,
                    params: { include: ['subcontrato','empresa','usuario','orden_compra','costo'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-pago-anticipado/SET_SOLICITUD', data);
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
