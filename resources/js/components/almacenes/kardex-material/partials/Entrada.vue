<template>
    <span>
         <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row" v-if="inventarios">
                                <div class="table-responsive" style="overflow-y: scroll;">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th class="encabezado">
                                                Fecha
                                            </th>
                                            <th class="encabezado">
                                                Unidad
                                            </th>
                                            <th class="encabezado">
                                                Entradas
                                            </th>
                                            <th class="encabezado">
                                                Salidas
                                            </th>
                                            <th class="encabezado">
                                                Existencia
                                            </th>
                                            <th class="encabezado">
                                                Adquirido
                                            </th>
                                            <th class="encabezado">
                                                Pagado
                                            </th>
                                            <th class="encabezado">
                                                x Pagar
                                            </th>
                                            <th class="encabezado">
                                                Referencia
                                            </th>
                                        </tr>
                                        <tr v-for="inventario in inventarios">
                                            <td style="text-align: center">
                                                {{inventario.fecha}}
                                            </td>
                                            <td style="text-align: center">
                                                {{inventario.unidad}}
                                            </td>
                                            <td style="text-align: right">
                                                {{inventario.entrada}}
                                            </td>
                                            <td style="text-align: right">
                                                {{inventario.salida}}
                                            </td>
                                            <td style="text-align: right">
                                                {{inventario.existencia}}
                                            </td>
                                            <td style="text-align: right">
                                                {{inventario.adquirido}}
                                            </td>
                                            <td style="text-align: right">
                                                {{inventario.pagado}}
                                            </td>
                                            <td style="text-align: right">
                                                {{inventario.x_pagar}}
                                            </td>
                                            <td style="text-align: center">
                                                {{inventario.referencia}}
                                            </td>
                                        </tr>
                                        <tr v-if="total_inv">
                                            <td class="encabezado"><b>TOTAL</b></td>
                                            <td></td>
                                            <td style="text-align: right">
                                                <b>{{total_inv.entrada}}</b>
                                            </td>
                                            <td style="text-align: right">
                                                <b>{{total_inv.salida}}</b>
                                            </td>
                                            <td style="text-align: right">
                                                <b>{{total_inv.existencia}}</b>
                                            </td>
                                            <td style="text-align: right">
                                                <b>{{total_inv.adquirido}}</b>
                                            </td>
                                            <td style="text-align: right">
                                                <b>{{total_inv.pagado}}</b>
                                            </td>
                                            <td style="text-align: right">
                                                <b>{{total_inv.x_pagar}}</b>
                                            </td>
                                            <td style="text-align: center">
                                            </td>
                                        </tr>
                                    </table>
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
        name: "entrada-historico-show",
        props: ['inventarios'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {

        }
    }
</script>
