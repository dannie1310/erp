<template>
    <span v-if="subcontrato">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                <b>Subcontrato</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <b>{{subcontrato.numero_folio_format}}</b>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{subcontrato.fecha_format}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Contrato:</td>
                                            <td style="text-align: right">
                                                <router-link :to="{name: 'proyectado-show', params:{id : subcontrato.id_contrato_proyectado }}" target="_blank">
                                                    <b><span style="color:black; text-decoration: underline">{{subcontrato.contrato_folio_format}}</span></b>
                                                </router-link>
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
    name: "EncabezadoSubcontrato",
    components: {},
    props: ["subcontrato"],
}
</script>

<style scoped>

</style>
