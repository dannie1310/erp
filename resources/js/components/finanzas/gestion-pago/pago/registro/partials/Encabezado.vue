<template>
    <span v-if="solicitud">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                <b>{{solicitud.tipo}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <b><span style="color:black; text-decoration: underline">{{solicitud.numero_folio}}</span></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{solicitud.fecha}}</b>
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
    name: "EncabezadoDocumento",
    components: {},
    props: ["solicitud"],
}
</script>

<style scoped>

</style>
