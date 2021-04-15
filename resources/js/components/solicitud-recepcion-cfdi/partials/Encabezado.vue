<template>
    <span>
        <span v-if="solicitud">
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-body">
                                <table style="font-size: 1.3em">
                                    <tbody>
                                        <tr>
                                            <td>Folio de Solicitud:</td>
                                            <td style="text-align: right">
                                                <b>{{solicitud.numero_folio}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fecha de Solicitud:</td>
                                            <td style="text-align: right">
                                                <b>{{solicitud.fecha_registro}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Referencia CFDI:</td>
                                            <td style="text-align: right">
                                                <b><CFDI v-bind:id="solicitud.cfdi.id" v-bind:txt="solicitud.cfdi.referencia"></CFDI></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Total CFDI:</td>
                                            <td style="text-align: right"><b>{{solicitud.cfdi.total_format}}</b></td>
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
    </span>
</template>

<script>
import CFDI from "../../fiscal/cfd/cfd-sat/CFDI";
export default {
    name: "Encabezado",
    components: {CFDI},
    props: ["solicitud"],
}
</script>

<style scoped>

</style>
