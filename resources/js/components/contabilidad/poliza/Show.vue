<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fa fa-list"></i> Información de Prepóliza
                        </h4>
                    </div>
                </div>
                <div v-if="poliza" class="row">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light"><b>Tipo Póliza SAO:</b><br>{{
                                    poliza.transaccionInterfaz.descripcion }}
                                </td>
                                <td class="bg-gray-light"><b>Fecha de Prepóliza:</b><br>
                                    {{ poliza.fecha}}
                                </td>
                                <td class="bg-gray-light"><b>Usuario Solicita:</b><br>
                                    {{ poliza.usuario_solicita }}
                                </td>
                                <td class="bg-gray-light"><b>Cuadre:</b><br>$
                                    {{ parseFloat(poliza.cuadre).formatMoney(2, '.', ',') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-gray-light"><b>Estatus:</b><br>
                                    <estatus-label :value="poliza.estatusPrepoliza"></estatus-label>
                                </td>
                                <td class="bg-gray-light">
                                    <b>Póliza Contpaq:</b>
                                    <br>
                                    {{ poliza.poliza_contpaq ? '#' + poliza.poliza_contpaq : '' }}
                                </td>
                                <td class="bg-gray-light"><b>Tipo de Póliza:</b><br>
                                    {{ poliza.tipoPolizaContpaq.descripcion }}
                                </td>
                                <td class="bg-gray-light"><b>Transacción Antecedente:</b><br>
                                    <span v-if="poliza.transaccionAntecedente">
                                        [{{ poliza.transaccionAntecedente.tipo.descripcion }}]  #{{ poliza.transaccionAntecedente.numero_folio }}
                                    </span>
                                    <span v-else-if="poliza.traspaso">
                                        [Traspaso] #{{ poliza.traspaso.numero_folio }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="bg-gray-light">
                                    <b>Concepto:</b><br>
                                    {{ poliza.concepto }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="poliza" class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="bg-gray-light">#</th>
                                <th class="bg-gray-light">Cuenta Contable</th>
                                <th class="bg-gray-light">Tipo Cuenta Contable</th>
                                <th class="bg-gray-light">Debe</th>
                                <th class="bg-gray-light">Haber</th>
                                <th class="bg-gray-light">Referencia</th>
                                <th class="bg-gray-light">Concepto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(movimiento, i) in poliza.movimientos.data">
                                <td>{{ i + 1 }}</td>
                                <td>{{ movimiento.cuenta_contable }}</td>
                                <td>{{ movimiento.tipoCuentaContable ? movimiento.tipoCuentaContable.descripcion : 'No registrada'}}</td>
                                <td>
                                    <span v-if="movimiento.id_tipo_movimiento_poliza == 1">
                                        ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}
                                    </span>
                                </td>
                                <td>
                                    <span v-if="movimiento.id_tipo_movimiento_poliza == 2">
                                        ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}
                                    </span>
                                </td>
                                <td>{{ movimiento.referencia }}</td>
                                <td>{{ movimiento.concepto }}</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" class="text-center" :class="color">
                                    <b>Sumas Iguales</b>
                                </th>
                                <th :class="color">
                                    <b>$&nbsp;{{(parseFloat(sumaDebe)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :class="color">
                                    <b>$&nbsp;{{(parseFloat(sumaHaber)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :class="color" colspan="2"></th>
                            </tr>
                            </tfoot>
                        </table>
                        <div class="col-sm-12" style="text-align: right">
                            <h4><b>Total de la Prepóliza:</b>
                                $&nbsp;{{ (parseFloat(poliza.total)).formatMoney(2, '.', ',') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import EstatusLabel from "./partials/EstatusLabel";

    export default {
        name: "poliza-show",
        components: {EstatusLabel},
        props: ['id'],

        mounted() {
            this.find({
                id: this.id,
                params: { include: 'transaccionAntecedente,movimientos,traspaso' }
            })
        },

        methods: {
            find(payload) {
                return this.$store.dispatch('contabilidad/poliza/find', payload);
            }
        },

        computed: {
            poliza() {
                return this.$store.getters['contabilidad/poliza/currentPoliza']
            },

            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            sumaDebe() {
                let result = 0;
                this.poliza.movimientos.data.forEach(function (movimiento, i) {
                    if (movimiento.id_tipo_movimiento_poliza == 1) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },

            sumaHaber() {
                let result = 0;
                this.poliza.movimientos.data.forEach(function (movimiento, i) {
                    if (movimiento.id_tipo_movimiento_poliza == 2) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },

            cuadrado() {
                return Math.abs(this.sumaDebe - this.sumaHaber) <= 0.99;
            },

            color() {
                if (!this.cuadrado) {
                    return 'bg-danger'
                } else {
                    return 'bg-gray'
                }
            }
        }
    }
</script>