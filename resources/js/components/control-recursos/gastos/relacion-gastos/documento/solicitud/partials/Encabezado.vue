<template>
    <span v-if="reembolso">
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
                                                    <b>Solicitud de Pago</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Folio:</td>
                                                <td style="text-align: right">
                                                    <b>{{reembolso.folio_compuesto}}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fecha:</td>
                                                <td style="text-align: right">
                                                    <b>{{reembolso.fecha_format}}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fecha Vencimiento:</td>
                                                <td style="text-align: right">
                                                    <b>{{reembolso.fecha_vencimiento}}</b>
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
    props: ["reembolso"],
}
</script>

<style scoped>

</style>
