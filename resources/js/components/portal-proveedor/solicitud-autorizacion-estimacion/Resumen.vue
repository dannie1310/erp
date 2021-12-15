<template>
    <span>
        <button type="button" @click="init()" class="btn btn-primary" >
            Resumen
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="resumenLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="resumenLabel">Resumen de Solicitud</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" v-if="estimacion && configuracion">
                                <div class="table-responsive">
                                    <table style="width: 100%" class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <th style="text-align: left" colspan="2">Importe Solicitud</th>
                                            <td style="text-align: right">{{estimacion.suma_importes}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left">Amortización de Anticipo</th>
                                            <td>{{estimacion.anticipo}}</td>
                                            <td style="text-align: right">{{ estimacion.monto_anticipo_aplicado_format }}</td>
                                        </tr>
                                        <tr v-if="configuracion.ret_fon_gar_antes_iva == 1">
                                            <th style="text-align: left">Retención de Fondo de Garantia</th>
                                            <td>{{estimacion.retencion}} %</td>
                                            <td style="text-align: right">{{estimacion.retencion_fondo_garantia}}</td>
                                        </tr>
                                        <tr v-if="configuracion.retenciones_antes_iva == '1'">
                                            <th style="text-align: left" colspan="2">Total Retenciones</th>
                                            <td style="text-align: right">{{estimacion.total_retenciones}}</td>
                                        </tr>
                                        <tr v-if="configuracion.retenciones_antes_iva == 1">
                                            <th style="text-align: left" colspan="2">Total Retenciones Liberadas</th>
                                            <td style="text-align: right">{{estimacion.total_retencion_liberadas}}</td>
                                        </tr>
                                        <tr v-if="configuracion.desc_pres_mat_antes_iva == 1">
                                            <th style="text-align: left" colspan="2">Total Deductivas</th>
                                            <td style="text-align: right">{{estimacion.total_deductivas}}</td>
                                        </tr>
                                        <tr v-if="configuracion.penalizacion_antes_iva == 1">
                                            <th style="text-align: left" colspan="2">Total Penalizaciones</th>
                                            <td style="text-align: right">{{estimacion.suma_penalizaciones}}</td>
                                        </tr>
                                         <tr v-if="configuracion.penalizacion_antes_iva == 1">
                                            <th style="text-align: left" colspan="2">Total Penalizaciones Liberadas</th>
                                            <td style="text-align: right">{{estimacion.suma_penalizaciones_liberadas}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left" colspan="2">Subtotal</th>
                                            <td style="text-align: right">{{estimacion.subtotal_orden_pago}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left" colspan="2">IVA</th>
                                            <td style="text-align: right">{{estimacion.iva_orden_pago}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left">Retención de IVA</th>
                                            <td>{{estimacion.retencion_iva_porcentaje}}</td>
                                            <td style="text-align: right">{{estimacion.retencion_iva_format}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left" colspan="2">Total</th>
                                            <td style="text-align: right">{{estimacion.total_orden_pago}}</td>
                                        </tr>
                                        <tr v-if="configuracion.ret_fon_gar_antes_iva == 0">
                                            <th style="text-align: left">Retención de Fondo de Garantia Solicitud</th>
                                            <td v-if="configuracion.ret_fon_gar_con_iva == 1">{{estimacion.retencion}} % + IVA</td>
                                            <td v-else>{{estimacion.retencion}} %</td>
                                            <td style="text-align: right">{{estimacion.retencion_fondo_garantia}}</td>
                                        </tr>
                                        <tr v-if="configuracion.desc_pres_mat_antes_iva == 0">
                                            <th style="text-align: left" colspan="2">Total Deductivas</th>
                                            <td style="text-align: right">{{estimacion.total_deductivas}}</td>
                                        </tr>
                                        <tr v-if="configuracion.retenciones_antes_iva == 0">
                                            <th style="text-align: left" colspan="2">Total Retenciones</th>
                                            <td style="text-align: right">{{estimacion.total_retenciones}}</td>
                                        </tr>
                                        <tr v-if="configuracion.retenciones_antes_iva == 0">
                                            <th style="text-align: left" colspan="2">Total Retenciones Liberadas</th>
                                            <td style="text-align: right">{{estimacion.total_retencion_liberadas}}</td>
                                        </tr>
                                        <tr v-if="configuracion.penalizacion_antes_iva == 0">
                                            <th style="text-align: left" colspan="2">Total Penalizaciones</th>
                                            <td style="text-align: right">{{estimacion.suma_penalizaciones}}</td>
                                        </tr>
                                        <tr v-if="configuracion.penalizacion_antes_iva == 0">
                                            <th style="text-align: left" colspan="2">Total Penalizaciones Liberadas</th>
                                            <td style="text-align: right">{{estimacion.suma_penalizaciones_liberadas}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left" colspan="2">Total Anticipo a Liberar</th>
                                            <td style="text-align: right">{{estimacion.total_anticipo_liberar}}</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left" colspan="2">Total a Pagar</th>
                                            <td style="text-align: right">{{ estimacion.monto_pagar_format }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
        name: "resumen-solicitud-autorizacion-avance",
        components: {},
        props: ['estimacion','base'],
        data() {
            return {
                configuracion : [],
            }
        },
        mounted() {
        },
        methods: {
            init(){
                this.configuracion = [];
                this.getConfiguraciones();
            },
            getConfiguraciones() {
                return this.$store.dispatch('finanzas/estimacion/indexProveedor', {
                    base: this.base, obra: this.estimacion.id_obra
                }).then(data => {
                    this.configuracion = data
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                });
            },
        }
    }
</script>

<style>

</style>
