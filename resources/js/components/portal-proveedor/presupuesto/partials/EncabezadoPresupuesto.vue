<template>
    <span v-if="presupuesto">
        <div class="row" >
            <div class="offset-md-8 col-md-4">
                <span class="pull-right">
                    <div class="card">
                        <div class="card-body">
                            <table style="font-size: 1.3em">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                            <b>Presupuesto</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Folio:</td>
                                        <td style="text-align: right">
                                            <b>{{presupuesto.numero_folio_format}}</b>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Fecha:</td>
                                        <td style="text-align: right">
                                            <b>{{presupuesto.fecha_format}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Contrato Proyectado:</td>
                                        <td style="text-align: right">
                                            <b><span style="color:black; text-decoration: underline">{{presupuesto.contrato_proyectado.numero_folio_format}}</span></b>
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
        name: "EncabezadoPresupuesto",
        components: {},
        props: ["presupuesto"],
    }
</script>

<style scoped>

</style>
