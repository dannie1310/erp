<template>
    <span v-if="subcontrato">
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
        name: "SubcontratoEncabezado",
        components: {},
        props: ["subcontrato"],
    }
</script>

<style scoped>

</style>
