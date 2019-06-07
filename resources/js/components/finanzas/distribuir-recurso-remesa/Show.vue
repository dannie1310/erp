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
                                    <div class="row" v-if="distribucion">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio Distribución:</b><br>{{distribucion.folio}}
                                                        </td>
                                                        <td class="bg-gray-light"><b>Fecha de Registro:</b><br>{{distribucion.fecha_registro}}
                                                        </td>
                                                        <td class="bg-gray-light"><b>Monto Total de Remesa:</b><br>$&nbsp; {{(parseFloat(distribucion.monto_autorizado)).formatMoney(2,'.',',')}}
                                                        </td>
                                                        <td class="bg-gray-light"><b>Monto de Está Distribución:</b><br>$ {{(parseFloat(distribucion.monto_distribuido)).formatMoney(2,'.',',')}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="bg-gray-light">
                                                            <b>Usuario de Registro:</b>
                                                            <br>
                                                            DFGD
                                                        </td>
                                                        <td colspan="2" class="bg-gray-light"><b>Estado:</b><br>
                                                             <estatus-label :value="distribucion.estado"></estatus-label>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="distribucion.estado.estado == -1">
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
                                                        <td colspan="4" class="bg-gray-light"><b>Información de la Remesa</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light">
                                                            <b>Año:</b><br>
                                                           {{distribucion.remesa_liberada.remesa.año}}
                                                        </td>
                                                        <td class="bg-gray-light">
                                                            <b>Semana:</b><br>
                                                            {{distribucion.remesa_liberada.remesa.semana}}
                                                        </td>
                                                        <td class="bg-gray-light">
                                                            <b>Remesa <br>{{distribucion.remesa_liberada.remesa.tipo}}</b>
                                                        </td>
                                                        <td class="bg-gray-light">
                                                            <b>Folio: <br>({{ distribucion.remesa_liberada.remesa.folio }})</b>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                        </div>

                    </div>
                    <div class="row" v-if="distribucion">
                     <div  class="col-12">
                         <div class="table-responsive">
                             <table class="table table-striped">
                                 <thead>
                                     <tr>
                                         <th>#</th>
                                         <th>Concepto</th>
                                         <th>Beneficiario</th>
                                         <th>Importe</th>
                                         <th>Tipo Cambio</th>
                                         <th>Importe con TC</th>
                                         <th>Tipo Cambio Actual</th>
                                         <th>Importe Pesos</th>
                                         <th>Cuenta Abono</th>
                                         <th>Cuenta Cargo</th>
                                         <th>Estado</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                    <tr v-for="(doc, i) in distribucion.partidas.data">
                                        <td>{{i+1}}</td>
                                        <td>{{doc.documento.concepto}}</td>
                                        <td>{{doc.documento.empresa ? doc.documento.empresa.razon_social : ''}}</td>
                                        <td>{{doc.documento.monto_total_format}}</td>
                                        <td>{{parseFloat(doc.documento.tipo_cambio).formatMoney(2, '.', ',') }}</td>
                                        <td>{{doc.documento.saldo_moneda_nacional_format}}</td>
                                        <td>{{doc.tipoCambioActual ? parseFloat(doc.tipoCambioActual.cambio).formatMoney(2, '.', ',') : '1.00'}}</td>
                                        <td>${{parseFloat(doc.importe_total).formatMoney(2, '.', ',') }}</td>
                                        <!--<td>{{doc.cuentaAbono}}{cuenta.banco.complemento.nombre_corto}} {{ cuenta.cuenta }}</td>-->
                                        <!--<td >{{ cuenta.abreviatura }} ({{cuenta.numero}})</td>-->
                                        <td></td>
                                    </tr>
                                 </tbody>
                             </table>
                         </div>
                     </div>
                        </div>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
    import EstatusLabel from "./partials/DistribuirEstatus";
    export default {
        name: "distribuir-recurso-remesa-show",
        components: {EstatusLabel},
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', null);
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/find', {
                    id: id,
                    params: { include: ['remesa_liberada.remesa.documento', 'partidas.documento.empresa','partidas.cuentaAbono.banco'] }
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