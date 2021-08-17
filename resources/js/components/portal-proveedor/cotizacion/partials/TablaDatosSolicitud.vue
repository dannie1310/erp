<template>
    <div class="row" v-if="solicitud">
        <div class="col-md-12">
            <span v-if="solicitud.tipo_transaccion == 17"><i class="fa fa-comment-dots"></i>Datos de Solicitud de Compra</span>
            <span v-if="solicitud.tipo_transaccion == 49"><i class="fa fa-comment-dots"></i>Datos de Contrato Proyectado</span>
            <table class="table table-bordered table-sm">
                <tr v-if="solicitud.tipo_transaccion == 17">
                    <th class="encabezado">
                        Área Responsable de Compra
                    </th>
                    <th class="encabezado">
                        Folio de Solicitud
                    </th>
                    <th class="encabezado">
                        Fecha de Solicitud
                    </th>
                    <th class="encabezado">
                        Concepto
                    </th>
                    <th class="encabezado">
                        Fecha de Registro
                    </th>
                </tr>
                <tr v-if="solicitud.tipo_transaccion == 49">
                    <th class="encabezado">
                        Contratista
                    </th>
                    <th class="encabezado">
                        Folio de Presupuesto
                    </th>
                    <th class="encabezado">
                        Folio de Contrato
                    </th>
                    <th class="encabezado">
                        Referencia de Contrato
                    </th>
                    <th class="encabezado">
                        Fecha de Presupuesto
                    </th>
                    <th class="encabezado">
                        Monto de Presupuesto
                    </th>
                    <th class="encabezado">
                        Moneda
                    </th>
                </tr>
                <tr v-if="solicitud.tipo_transaccion == 17">
                    <td>
                        {{solicitud.area_compradora}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud.numero_folio_format}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud.fecha_format}}
                    </td>
                    <td >
                        {{solicitud.concepto}}
                    </td>
                     <td style="text-align: center">
                        {{solicitud.fecha_registro}}
                    </td>
                </tr>
                <tr v-if="solicitud.tipo_transaccion == 49">
                    <td>
                        {{solicitud.razon_social}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud.numero_folio_format}}
                    </td>
                    <td style="text-align: center">
                        <b><span style="color:black; text-decoration: underline">{{solicitud.numero_folio_format}}</span></b>
                    </td>
                    <td>
                        {{solicitud.referencia }}
                    </td>
                    <td style="text-align: center">
                        {{ solicitud.fecha_format }}
                    </td>
                    <td style="text-align: center">
                        {{solicitud.monto_format}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud.moneda_conversion}}
                    </td>
                </tr>
                <template v-if="solicitud.observaciones!='...'">
                    <tr>
                        <th colspan="5" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="5">
                            {{solicitud.observaciones}}
                        </td>
                    </tr>
                </template>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "TablaDatosSolicitud",
    components: {},
    props: ['solicitud'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
