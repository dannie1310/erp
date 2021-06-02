<template>
    <span v-if="contrato_proyectado">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e">
                                                <b>Contrato Proyectado</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <router-link :to="{name: 'proyectado-show', params:{id : contrato_proyectado.id }}" target="_blank">
                                                    <b><span style="color:black; text-decoration: underline">{{contrato_proyectado.numero_folio_format}}</span></b>
                                                </router-link>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{contrato_proyectado.fecha_format}}</b>
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
    name: "EncabezadoContratoProyectado",
    components: {},
    props: ["contrato_proyectado"],
}
</script>

<style scoped>

</style>
