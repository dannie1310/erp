<template>
    <div class="row" v-if="estimacion">
        <div class="col-md-12">
            <span><i class="fa fa-comment-dollar"></i>Datos de Estimación</span>
            <table class="table table-sm">
                <tr>
                    <th class="encabezado" colspan="8">
                        {{estimacion.razon_social}}
                    </th>
                </tr>
                <tr >
                    <th class="encabezado">
                        Folio de Subcontrato
                    </th>
                    <th class="encabezado">
                        Folio de Estimación
                    </th>
                    <th class="encabezado">
                        Fecha de Estimación
                    </th>
                    <th class="encabezado">
                        Inicio de Estimación
                    </th>
                    <th class="encabezado">
                        Fin de Estimación
                    </th>
                    <th class="encabezado">
                        Subtotal
                    </th>
                    <th class="encabezado">
                        IVA
                    </th>
                    <th class="encabezado">
                        Total
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
                    <td style="text-align: center">
                        {{estimacion.fecha_inicial}}
                    </td>
                    <td style="text-align: center">
                        {{estimacion.fecha_final}}
                    </td>
                    <td style="text-align: right">
                        {{estimacion.subtotal}}
                    </td>
                    <td style="text-align: right">
                        {{estimacion.iva}}
                    </td>
                    <td style="text-align: right">
                        {{estimacion.total}}
                    </td>
                </tr>
                <tr>
                    <th style="border: none" colspan="8"></th>
                </tr>
                <template v-if="estimacion.observaciones!=null">
                    <tr>
                        <th colspan="8" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="8">
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
