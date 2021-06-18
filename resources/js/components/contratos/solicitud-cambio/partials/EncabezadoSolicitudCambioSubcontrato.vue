<template>
    <span v-if="solicitud_cambio">
        <div class="row" >
            <div class="offset-md-8 col-md-4">
                <span class="pull-right">
                    <div class="card">
                        <div class="card-body">
                            <table style="font-size: 1.3em">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                            <b>Solicitud de Cambio</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Folio:</td>
                                        <td style="text-align: right">
                                            <b>{{solicitud_cambio.numero_folio_format}}</b>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Fecha:</td>
                                        <td style="text-align: right">
                                            <b>{{solicitud_cambio.fecha_format}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Subcontrato:</td>
                                        <td style="text-align: right">
                                            <b><span >{{solicitud_cambio.subcontrato.numero_folio_format}}</span></b>
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
    name: "EncabezadoSolicitudCambioSubcontrato",
    components: {},
    props: ["solicitud_cambio"],
}
</script>

<style scoped>

</style>
