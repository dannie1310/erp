<template>
    <span>
         <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row" v-if="salidas">
                                <div class="table-responsive" style="overflow-y: scroll;">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="encabezado">
                                                    Fecha
                                                </th>
                                                <th class="encabezado">
                                                    Cantidad
                                                </th>
                                                <th class="encabezado">
                                                    Importe Total
                                                </th>
                                                <th class="encabezado">
                                                    Importe Pagado
                                                </th>
                                                <th class="encabezado">
                                                    Importe Por Pagar
                                                </th>
                                                <th class="encabezado">
                                                    Referencia
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody v-for="salida in salidas">
                                            <tr v-if="salida.id != undefined && salida.concepto == undefined && salida.almacen == undefined">
                                                <td style="text-align: center">
                                                    {{salida.fecha}}
                                                </td>
                                                <td style="text-align: center">
                                                    {{salida.cantidad}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{salida.total}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{salida.pagado}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{salida.por_pagar}}
                                                </td>
                                                <td style="text-align: center">
                                                    {{salida.referencia}}
                                                </td>
                                            </tr>
                                            <tr v-else-if="salida.concepto != undefined" style="background-color: yellow;">
                                                <td style="text-align: left;" colspan="6">
                                                    {{salida.concepto}}
                                                </td>
                                            </tr>
                                            <tr v-else-if="salida.almacen != undefined" style="background-color: yellow;">
                                                <td style="text-align: center;" colspan="6">
                                                    {{salida.almacen}}
                                                </td>
                                            </tr>
                                            <tr v-else style="background-color: yellow;">
                                                <td style="text-align: center">
                                                    Subtotal
                                                </td>
                                                <td style="text-align: center">
                                                    {{salida.suma_cantidad}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{salida.suma_total}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{salida.suma_pagado}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{salida.suma_por_pagar}}
                                                </td>
                                                <td style="text-align: center">
                                                </td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr v-if="totales" style="background-color: yellow;">
                                                <td class="encabezado" style="text-align: right"><b>TOTAL</b></td>
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
                                        </tfoot>
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
        name: "salida-historico-show",
        props: ['salidas', 'totales'],
        data() {
            return {

            }
        },
        methods: {

        }
    }
</script>
