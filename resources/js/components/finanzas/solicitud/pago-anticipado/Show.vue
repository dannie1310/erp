<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> INFORMACIÓN DE SOLICITUD DE PAGO ANTICIPADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row" v-if="pagoAnticipado">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha de Solicitud:</b></label>
                                            {{pagoAnticipado.cumplimiento}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Limite de Pago:</b></label>
                                            {{ pagoAnticipado.vencimiento}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="pagoAnticipado">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <div class="form-group" v-if="pagoAnticipado.empresa">
                                            <label><b>Empresa:</b></label>
                                            {{ pagoAnticipado.empresa.razon_social}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="pagoAnticipado">
                                <div class="col-md-6">
                                    <div class="form-group error-content" v-if="pagoAnticipado.subcontrato">
                                        <label>Tipo de Transacción:</label>
                                        {{pagoAnticipado.subcontrato.tipo_nombre}}
                                    </div>
                                    <div class="form-group error-content" v-if="pagoAnticipado.orden_compra">
                                        <label>Tipo de Transacción:</label>
                                        {{pagoAnticipado.orden_compra.tipo_nombre}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content" v-if="pagoAnticipado.subcontrato">
                                        <label>Transacción:</label>
                                        ({{pagoAnticipado.subcontrato.tipo_nombre}}){{pagoAnticipado.subcontrato.numero_folio_format}}({{pagoAnticipado.subcontrato.referencia}})
                                    </div>
                                    <div class="form-group error-content" v-if="pagoAnticipado.orden_compra">
                                        <label>Transacción:</label>
                                        ({{pagoAnticipado.orden_compra.tipo_nombre}}){{pagoAnticipado.orden_compra.numero_folio_format}}({{pagoAnticipado.orden_compra.referencia}})
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row" v-if="pagoAnticipado">
                                            <div class="col-6" >
                                                <h3>Información de la Transacción</h3>
                                            </div>
                                            <div class="col-6"  v-if="pagoAnticipado.subcontrato">
                                                <h6 align="right">Fecha: {{pagoAnticipado.subcontrato.fecha_format}}</h6>
                                            </div>
                                            <div class="col-6"  v-if="pagoAnticipado.orden_compra">
                                                <h6 align="right">Fecha: {{pagoAnticipado.orden_compra.fecha_format}}</h6>
                                            </div>
                                        </div>
                                            <div class="row"  v-if="pagoAnticipado">
                                                <div class="table-responsive col-md-12">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                        <tr  v-if="pagoAnticipado.subcontrato">
                                                            <td class="bg-gray-light"><b>Número de Folio:</b><br></td>
                                                            <td>
                                                                 {{pagoAnticipado.subcontrato.numero_folio_format}}
                                                            </td>
                                                        </tr>
                                                        <tr v-if="pagoAnticipado.orden_compra">
                                                            <td class="bg-gray-light" ><b>Número de Folio: </b><br></td>
                                                            <td>
                                                                {{pagoAnticipado.orden_compra.numero_folio_format}}
                                                            </td>
                                                        </tr>
                                                        <tr v-if="pagoAnticipado.empresa">
                                                            <td><b>Empresa:</b><br></td>
                                                            <td>
                                                                {{ pagoAnticipado.empresa.razon_social}}
                                                            </td>
                                                        </tr>
                                                        <tr v-if="pagoAnticipado.subcontrato">
                                                            <td class="bg-gray-light" ><b>Referencia:</b><br></td>
                                                            <td>
                                                                {{pagoAnticipado.subcontrato.referencia}}
                                                            </td>
                                                        </tr>
                                                        <tr  v-if="pagoAnticipado.orden_compra">
                                                            <td class="bg-gray-light" ><b>Referencia:</b><br></td>
                                                            <td>
                                                                {{pagoAnticipado.orden_compra.referencia}}
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row" align="right"  v-if="pagoAnticipado">
                                                <div class="table-responsive col-md-12">
                                                    <div class="col-6">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <tbody>
                                                                    <tr v-if="pagoAnticipado.subcontrato">
                                                                        <th style="width:50%" class="bg-gray-light">Subtotal:</th>
                                                                        <td class="bg-gray-light" align="right">{{pagoAnticipado.subcontrato.subtotal_format}}</td>
                                                                    </tr>
                                                                    <tr  v-if="pagoAnticipado.orden_compra">
                                                                        <th style="width:50%" class="bg-gray-light">Subtotal:</th>
                                                                        <td class="bg-gray-light" align="right">{{pagoAnticipado.orden_compra.subtotal_format}}</td>
                                                                    </tr>
                                                                    <tr v-if="pagoAnticipado.subcontrato">
                                                                        <th>IVA:</th>
                                                                        <td align="right">{{pagoAnticipado.subcontrato.impuesto_format}}</td>
                                                                    </tr>
                                                                    <tr  v-if="pagoAnticipado.orden_compra">
                                                                        <th>IVA:</th>
                                                                        <td align="right">{{pagoAnticipado.orden_compra.impuesto_format}}</td>
                                                                    </tr>
                                                                    <tr v-if="pagoAnticipado.subcontrato">
                                                                        <th class="bg-gray-light">Total:</th>
                                                                        <td class="bg-gray-light" align="right">{{pagoAnticipado.subcontrato.monto_format}}</td>
                                                                    </tr>
                                                                    <tr  v-if="pagoAnticipado.orden_compra">
                                                                        <th class="bg-gray-light">Total:</th>
                                                                        <td class="bg-gray-light" align="right">{{pagoAnticipado.orden_compra.monto_format}}</td>
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
                            <div class="row" v-if="pagoAnticipado">

                                <div class="col-md-12" align="right">
                                    <div class="form-group error-content">
                                        <div class="form-group" v-if="pagoAnticipado.empresa">
                                            <label><b>Monto:</b></label>
                                            {{ pagoAnticipado.monto_format}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Observaciones -->
                                <div class="col-md-12">
                                    <div class="form-group error-content"  v-if="pagoAnticipado">
                                        <label>Observaciones:</label><br>
                                        {{pagoAnticipado.observaciones}}
                                    </div>
                                </div>
                            </div>
                            <div class="row"  v-if="pagoAnticipado">
                                <!-- Costos -->
                                <div class="col-md-12"  v-if="pagoAnticipado.costo">
                                    <div class="form-group error-content">
                                        <label>Costos:</label>
                                        {{pagoAnticipado.costo.descripcion}}
                                    </div>
                                </div>
                            </div>
                             <div class="row" v-if="pagoAnticipado">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha y Hora de Registro:</b></label>
                                            {{pagoAnticipado.fecha_format}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group" v-if="pagoAnticipado.usuario">
                                            <label><b>Registro:</b></label>
                                            {{ pagoAnticipado.usuario.nombre}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row" v-if="pagoAnticipado">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <div class="form-group" v-if="pagoAnticipado.estado === 0">
                                            <label><b>Estado:</b></label>
                                            REGISTRADO
                                        </div>
                                        <div class="form-group" v-if="pagoAnticipado.estado === 2 || pagoAnticipado.estado === -2 ">
                                            <label><b>Estado:</b></label>
                                            CANCELADO
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