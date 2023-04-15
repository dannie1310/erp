<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row" v-if="movimientos">
                                <div class="table-responsive" style="overflow-y: scroll;">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="encabezado">
                                                    Fecha
                                                </th>
                                                <th class="encabezado">
                                                    Fecha de Registro
                                                </th>
                                                <th class="encabezado">
                                                    Folio
                                                </th>
                                                <th class="encabezado">
                                                    Cantidad Entrada
                                                </th>
                                                <th class="encabezado">
                                                    Cantidad Salida
                                                </th>
                                                <th class="encabezado">
                                                    Saldo Resultante
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody v-for="movimiento in movimientos">
                                            <tr>
                                                <td style="text-align: center">
                                                    {{movimiento.Fecha}}
                                                </td>
                                                <td style="text-align: center">
                                                    {{movimiento.FechaHoraRegistro}}
                                                </td>
                                                <td style="text-align: center">
                                                    {{movimiento.folio}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{movimiento.cantidad_entrada}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{movimiento.cantidad_salida}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{movimiento.saldo_restante}}
                                                </td>
                                            </tr>
                                        </tbody>
                                       <!-- <tfoot>
                                            <tr v-if="totales" style="background-color: yellow;">
                                                <td class="encabezado"><b>TOTAL</b></td>
                                                <td style="text-align: right">
                                                    <b>{{totales.cantidad}}</b>
                                                </td>
                                                <td style="text-align: right">
                                                    <b>{{totales.total}}</b>
                                                </td>
                                                <td style="text-align: right">
                                                    <b>{{totales.pagado}}</b>
                                                </td>
                                                <td style="text-align: right">
                                                    <b>{{totales.x_pagar}}</b>
                                                </td>
                                                <td style="text-align: center">
                                                </td>
                                            </tr>
                                        </tfoot>-->
                                    </table>
                                </div>
                            </div>
                            <div class="row" v-else>
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "Movimiento",
    props: ['movimientos', 'totales'],
    data() {
        return {
            cargando: false,
        }
    },
}
</script>

<style scoped>

</style>
