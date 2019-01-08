<template>
    <div class="row" v-if="!cargando">
        <div class="col-12">
            <!-- Main content -->
            <div class="invoice p-3 mb-3">
                <!-- title row -->
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fa fa-list"></i>  Información de Prepóliza
                        </h4>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- info row -->
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="bg-gray-light"><b>Tipo Póliza SAO:</b><br>{{ poliza.transaccionInterfaz.descripcion }}</td>
                            <td class="bg-gray-light"><b>Fecha de Prepóliza:</b><br><input type="date" class="form-control" v-model="poliza.fecha"/></td>
                            <td class="bg-gray-light"><b>Usuario Solicita:</b><br>{{ poliza.usuario_solicita }}</td>
                            <td class="bg-gray-light"><b>Cuadre:</b><br>$ {{ parseFloat(poliza.cuadre).formatMoney(2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td class="bg-gray-light"><b>Estatus:</b><br><estatus-label :value="poliza.estatusPrepoliza"></estatus-label></td>
                            <td class="bg-gray-light"><b>Póliza Contpaq:</b><br>#{{ poliza.poliza_contpaq }}</td>
                            <td class="bg-gray-light"><b>Tipo de Póliza:</b><br>{{ poliza.tipoPolizaContpaq.descripcion }}</td>
                            <td class="bg-gray-light"><b>Transacción Antecedente:</b><br>
                                <span v-if="poliza.factura">
                                    [{{ poliza.factura.tipo.descripcion }}]  #{{ poliza.factura.numero_folio }}
                                </span>
                                <span v-else-if="poliza.traspaso">
                                    [Traspaso] #{{ poliza.traspaso.numero_folio }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="bg-gray-light">
                                <b>Concepto:</b><br>
                                <textarea name="concepto" type="text" class="form-control" v-model="poliza.concepto"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.row -->

                <!-- Table row -->
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped" v-if="!cargando">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Cuenta Contable</th>
                                <th>Tipo Cuenta Contable</th>
                                <th>Tipo</th>
                                <th>Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(partida, i) in poliza.movimientos.data">
                                <td>{{ i + 1 }}</td>
                                <td>{{ partida.cuenta_contable }}</td>
                                <td>{{ i + 1 }}</td>
                                <td>{{ i + 1 }}</td>
                                <td>{{ i + 1 }}</td>
                                <td>{{ i + 1 }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- this row will not appear when printing -->
                <!--<div class="row no-print">
                    <div class="col-12">
                        <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
                        <button type="button" class="btn btn-success float-right"><i class="fa fa-credit-card"></i> Submit
                            Payment
                        </button>
                        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                            <i class="fa fa-download"></i> Generate PDF
                        </button>
                    </div>
                </div>-->
            </div>
            <!-- /.invoice -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</template>

<script>
    import EstatusLabel from "./partials/EstatusLabel";

    export default {
        name: "poliza-edit",
        components: {EstatusLabel},
        props: ['id'],
        data() {
            return {
                poliza: null,
            }
        },
        mounted() {
            this.find(this.id).then(data => {
                this.poliza = data;
            });
        },

        methods: {
            find(id) {
                return this.$store.dispatch('contabilidad/poliza/find', {id: id, params: {include: 'factura,movimientos,traspaso'}})

            }
        },

        computed: {
            cargando() {
                return this.$store.getters['contabilidad/poliza/cargando']
            }
        }
    }
</script>

<style scoped>
    textarea[name="concepto"] {
        resize: none;
    }
</style>