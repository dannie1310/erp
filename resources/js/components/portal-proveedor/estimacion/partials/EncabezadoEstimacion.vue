<template>
    <span v-if="estimacion">
        <div class="row">
            <div class="offset-md-8 col-md-4">
                <span class="pull-right">
                    <div class="card">
                        <div class="card-body">
                            <table style="font-size: 1.3em">
                                <tbody>
                                <tr>
                                    <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                        <b>Estimación</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Folio:</td>
                                    <td style="text-align: right">
                                        <b><span style="color:black; ">{{estimacion.folio}}</span></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Fecha:</td>
                                    <td style="text-align: right">
                                        <b>{{estimacion.fecha}}</b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "EncabezadoEstimacionProveedor",
        components: {},
        props: ["estimacion"],
    }
</script>

<style scoped>

</style>
