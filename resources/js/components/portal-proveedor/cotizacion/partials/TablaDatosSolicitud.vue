<template>
    <div class="row" v-if="solicitud_compra">
        <div class="col-md-12">
            <span v-if="solicitud_compra.tipo_transaccion == 17"><i class="fa fa-comment-dots"></i>Datos de Solicitud de Compra</span>
            <span v-if="solicitud_compra.tipo_transaccion == 50"><i class="fa fa-comment-dots"></i>Datos de Contrato Proyectado</span>
            <table class="table table-bordered table-sm">
                <tr v-if="solicitud_compra.tipo_transaccion == 17">
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
                <tr v-if="solicitud_compra.tipo_transaccion == 50">
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
                <tr v-if="solicitud_compra.tipo_transaccion == 17">
                    <td>
                        {{solicitud_compra.area_compradora}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_compra.numero_folio_format}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_compra.fecha_format}}
                    </td>
                    <td >
                        {{solicitud_compra.concepto}}
                    </td>
                     <td style="text-align: center">
                        {{solicitud_compra.fecha_registro}}
                    </td>
                </tr>
                <tr v-if="solicitud_compra.tipo_transaccion == 49">
                    <td>
                        {{solicitud_compra.razon_social}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_compra.numero_folio_format}}
                    </td>
                    <td style="text-align: center">
                        <b><span style="color:black; text-decoration: underline">{{solicitud_compra.numero_folio_format}}</span></b>
                    </td>
                    <td>
                        {{solicitud_compra.referencia }}
                    </td>
                    <td style="text-align: center">
                        {{ solicitud_compra.fecha_format }}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_compra.monto_format}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_compra.moneda_conversion}}
                    </td>
                </tr>
                <template v-if="solicitud_compra.observaciones!='...'">
                    <tr>
                        <th colspan="5" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="5">
                            {{solicitud_compra.observaciones}}
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
    props: ['solicitud_compra'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
