<template>
    <span v-if="factura">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table style="font-size: 1.3em">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                    <b>Factura</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Serie:</td>
                                                <td style="text-align: right">
                                                    <b><span style="color:black;">{{factura.serie}}</span></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Folio:</td>
                                                <td style="text-align: right">
                                                    <router-link :to="{name: 'factura-recurso-show', params:{id : factura.id }}" target="_blank">
                                                        <b><span style="color:black; text-decoration: underline">{{factura.folio_format}}</span></b>
                                                    </router-link>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fecha:</td>
                                                <td style="text-align: right">
                                                    <b>{{factura.fecha_format}}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
    name: "Encabezado",
    components: {},
    props: ["factura"],
}
</script>

<style scoped>

</style>
