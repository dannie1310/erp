<template>
    <span v-if="solicitud_compra">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                <b v-if="solicitud_compra.tipo_transaccion == 17">Solicitud de Compra</b>
                                                <b v-if="solicitud_compra.tipo_transaccion == 50">Contrato Proyectado</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <b><span style="color:black; text-decoration: underline">{{solicitud_compra.numero_folio_format}}</span></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{solicitud_compra.fecha_format}}</b>
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
    name: "EncabezadoSolicitud",
    components: {},
    props: ["solicitud_compra"],
}
</script>

<style scoped>

</style>
