<template>
    <div class="row" v-if="estimacion">
        <div class="col-md-12">
            <span><i class="fa fa-comment-dollar"></i>Datos de Estimación</span>
            <table class="table table-sm">
                <tr>
                    <th class="encabezado" colspan="5">
                        {{estimacion.razon_social}}
                    </th>
                </tr>
                <tr >
                    <th class="encabezado" rowspan="2" style="text-align: center">
                        Folio de Subcontrato
                    </th>
                    <th class="encabezado" rowspan="2" style="text-align: center">
                        Folio de Estimación
                    </th>
                    <th class="encabezado" rowspan="2" style="text-align: center">
                        Fecha de Estimación
                    </th>
                    <th class="encabezado" colspan="2">
                        Periodo de estimación
                    </th>
                </tr>

                <tr>
                    <th class="encabezado">
                        Inicio
                    </th>
                    <th class="encabezado">
                        Término
                    </th>
                </tr>
                <tr>
                    <td style="text-align: center">
                        {{estimacion.folio_subcontrato}}
                    </td>
                    <td style="text-align: center">
                        <span style="color:black; text-decoration: underline">{{estimacion.folio}}</span>
                    </td>
                    <td style="text-align: center">
                        {{estimacion.fecha}}
                    </td>
                    <td>
                        {{estimacion.fecha_inicial}}
                    </td>
                    <td>
                        {{estimacion.fecha_final}}
                    </td>
                </tr>
                <tr>
                    <th style="border: none" colspan="5"></th>
                </tr>
                <tr>
                    <th colspan="5" class="encabezado">Totales</th>
                </tr>
                <tr>

                    <th class="encabezado" colspan="2">
                        Subtotal
                    </th>
                    <th class="encabezado">
                        IVA
                    </th>
                    <th class="encabezado" colspan="2">
                        Total
                    </th>
                </tr>
                <tr>
                    <td style="text-align: right" colspan="2">
                        {{estimacion.subtotal}}
                    </td>
                    <td style="text-align: right">
                        {{estimacion.iva}}
                    </td>
                    <td style="text-align: right" colspan="2">
                        {{estimacion.total}}
                    </td>

                </tr>
                <tr>
                    <th style="border: none" colspan="5"></th>
                </tr>
                <template v-if="estimacion.observaciones!=null">
                    <tr>
                        <th colspan="5" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="5">
                            {{estimacion.observaciones}}
                        </td>
                    </tr>
                </template>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "TablaDatosEstimacionProveedor",
    components: {},
    props: ['estimacion'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

td, th{
    border: 1px #dee2e6 solid;
}

</style>
