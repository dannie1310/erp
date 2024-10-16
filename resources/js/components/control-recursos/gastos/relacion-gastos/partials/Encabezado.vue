<template>
    <span v-if="relacion">
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
                                                    <b>Relación de Gastos</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Folio:</td>
                                                <td style="text-align: right">
                                                    <router-link :to="{name: 'relacion-gasto-show', params:{id : relacion.id }}" target="_blank">
                                                        <b><span style="color:black; text-decoration: underline">{{relacion.folio}}</span></b>
                                                    </router-link>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fecha Inicial:</td>
                                                <td style="text-align: right">
                                                    <b>{{relacion.fecha_inicio_format}}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fecha Final:</td>
                                                <td style="text-align: right">
                                                    <b>{{relacion.fecha_final_format}}</b>
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
    props: ["relacion"],
}
</script>

<style scoped>

</style>
