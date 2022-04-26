<template>
    <span v-if="avance">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                <b>Avance de Subcontrato</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Folio:</td>
                                            <td style="text-align: right">
                                                <router-link :to="{name: 'avance-subcontrato-show', params:{id : avance.id }}" target="_blank">
                                                    <b><span style="color:black; text-decoration: underline">{{avance.numero_folio_format}}</span></b>
                                                </router-link>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha:</td>
                                            <td style="text-align: right">
                                                <b>{{avance.fecha_format}}</b>
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
    name: "Encabezado",
    components: {},
    props: ["avance"],
}
</script>

<style scoped>

</style>
