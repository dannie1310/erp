<template>
    <span v-if="cotizacion_compra">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                <b>Cotización de Compra</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <router-link :to="{name: 'cotizacion-show', params:{id : cotizacion_compra.id }}" target="_blank">
                                                    <b><span style="color:black; text-decoration: underline">{{cotizacion_compra.numero_folio_format}}</span></b>
                                                </router-link>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{cotizacion_compra.fecha_format}}</b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </span>
                </div>
            </div>
            <br>
        </span>
</template>

<script>

export default {
    name: "EncabezadoCotizacionCompra",
    components: {},
    props: ["cotizacion_compra"],
}
</script>

<style scoped>

</style>
