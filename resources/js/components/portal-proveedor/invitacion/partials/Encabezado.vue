<template>
    <span v-if="invitacion">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                <b>Invitación a Cotizar</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <b>{{invitacion.numero_folio_format}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{invitacion.fecha_hora_format}}</b>
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
    name: "InvitacionCompraEncabezado",
    components: {},
    props: ["invitacion"],
}
</script>

<style scoped>

</style>
