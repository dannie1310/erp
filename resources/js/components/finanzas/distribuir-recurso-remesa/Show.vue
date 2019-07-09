<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4> <i class="fa fa-list-alt"></i> DISTRIBUCIÓN DE RECURSOS AUTORIZADOS DE LA REMESA </h4>
                    </div>
                </div>
                <div v-if="distribucion" class="row">
                    <div class="table-responsive col-12">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light">
                                    <b>Folio Distribución:</b>
                                </td>
                                <td class="bg-gray-light">
                                   {{distribucion.folio}}
                                </td>
                                <td class="bg-gray-light">
                                    <b>Fecha de Registro:</b>
                                </td>
                                <td class="bg-gray-light">
                                   {{distribucion.fecha_registro}}
                                </td>
                                <td class="bg-gray-light">
                                    <b>Monto Total de Remesa:</b>
                                </td>
                                <td class="bg-gray-light text-right">
                                    $&nbsp; {{(parseFloat(distribucion.monto_autorizado)).formatMoney(2,'.',',')}}
                                </td>
                                <td class="bg-gray-light">
                                    <b>Monto de Está Distribución:</b>
                                </td>
                                <td class="bg-gray-light text-right">
                                    $ {{(parseFloat(distribucion.monto_distribuido)).formatMoney(2,'.',',')}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-gray-light"><b>Estado:</b><br> </td>
                                <td colspan="2" class="bg-gray-light"><estatus-label :value="distribucion.estado"></estatus-label></td>

                                <td colspan="2" class="bg-gray-light">
                                    <b>Usuario de Registro:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{distribucion.usuario_registro.nombre}}
                                </td>
                            </tr>
                            <tr v-if="distribucion.estado.estado == -1">
                                <td colspan="4" class="bg-gray-light">
                                    <b>Usuario de Cancelación</b>
                                </td>
                                <td colspan="4" class="bg-gray-light">
                                    {{distribucion.usuario_cancelo.nombre}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" class="bg-gray-light"><b>Información de la Remesa</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Año:</b><br>
                                    {{distribucion.remesa_liberada.remesa.año}}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Semana:</b><br>
                                    {{distribucion.remesa_liberada.remesa.semana}}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Tipo de Remesa: </b>
                                    <br>{{distribucion.remesa_liberada.remesa.tipo}}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Folio: <br>({{ distribucion.remesa_liberada.remesa.folio }})</b>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-if="distribucion" class="row">
                    <div  class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Concepto</th>
                                <th>Beneficiario</th>
                                <th>Importe</th>
                                <th>Tipo Cambio</th>
                                <th>Importe con TC</th>
                                <th>Tipo Cambio Actual!!</th>
                                <th>Importe Pesos</th>
                                <th>Cuenta Abono</th>
                                <th>Cuenta Cargo</th>
                                <th v-if="distribucion.estado.estado === 3">Transaccion de Pago</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(doc, i) in distribucion.partidas.data">
                                <td>{{i+1}}</td>
                                <td>{{doc.documento.concepto}}</td>
                                <td>{{doc.documento.empresa ? doc.documento.empresa.razon_social : ''}}</td>
                                <td class="text-right">{{doc.documento.monto_total_format}}</td>
                                <td class="text-right">{{parseFloat(doc.documento.tipo_cambio).formatMoney(2, '.', ',') }}</td>
                                <td class="text-right">{{doc.documento.saldo_moneda_nacional_format}}</td>
                                <td class="text-right">{{doc.moneda  &&  doc.moneda.tipo != 1? parseFloat(doc.moneda.tipo_cambio).formatMoney(2, '.', ',') : '1.00'}}</td>
                                <td class="text-right">${{doc.moneda  &&  doc.moneda.tipo != 1?parseFloat((doc.documento.monto_total * doc.moneda.tipo_cambio)).formatMoney(2, '.', ',') :parseFloat((doc.documento.monto_total)).formatMoney(2, '.', ',') }}</td>
                                <td>{{doc.cuentaAbono.banco.complemento.nombre_corto}} {{doc.cuentaAbono.cuenta}}</td>
                                <td>{{ doc.cuentaCargo.abreviatura }} ({{doc.cuentaCargo.numero}})</td>
                                <td v-if="distribucion.estado.estado === 3 && doc.transaccion"> [ {{doc.transaccion.tipo.descripcion}} ], #{{doc.transaccion.numero_folio}}</td>
                                <td><partida-estatus :value="doc.estado"></partida-estatus></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PartidaEstatus from './partials/PartidaEstatus';
    import EstatusLabel from "./partials/DistribuirEstatus";
    export default {
        name: "distribuir-recurso-remesa-show",
        components: {EstatusLabel, PartidaEstatus},
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        mounted() {
            this.$Progress.start();
            this.find()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', null);
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/find', {
                    id: this.id,
                    params: {
                        include: ['remesa_liberada.remesa.documento', 'partidas.documento.empresa','partidas.cuentaAbono.banco', 'partidas.transaccion', 'usuario_cancelo'],
                    }
                }).then(data => {
                    this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', data);
                }) .finally(() => {
                    this.cargando = false;
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
