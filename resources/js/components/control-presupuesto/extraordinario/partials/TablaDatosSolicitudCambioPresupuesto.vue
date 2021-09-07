<template>
    <div class="row" v-if="solicitud_cambio">
        <div class="col-md-12">
            <span><i class="fa fa-stack-exchange"></i>Datos de Solicitud de Cambio</span>
            <table class="table table-sm">
                <tr >
                    <th class="encabezado">
                        Folio de Solicitud
                    </th>
                    <th class="encabezado">
                        Tipo de Solicitud
                    </th>
                    <th class="encabezado">
                        Área Solicitante
                    </th>
                    <th class="encabezado">
                        Monto de Solicitud
                    </th>
                    <th class="encabezado">
                        Estado de Solicitud
                    </th>
                </tr>
                <tr>
                    <td style="text-align: center">
                        {{ solicitud_cambio.numero_folio_format }}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_cambio.tipo_orden}}
                    </td>
                    <td style="text-align: center">
                        {{solicitud_cambio.area_solicitante}}
                    </td>
                    <td style="text-align: right">
                        {{ solicitud_cambio.importe_afectacion_format }}
                    </td>
                    <td style="text-align: center">
                        <EstatusLabelGlobal v-bind:value="solicitud_cambio.estado_label"></EstatusLabelGlobal>
                    </td>
                </tr>
                <tr>
                    <td style="border: none" colspan="5"></td>
                </tr>
                <tr >
                    <th class="encabezado" colspan="5">
                        Motivo
                    </th>
                </tr>
                <tr >
                    <td  colspan="5">
                        {{ solicitud_cambio.motivo }}
                    </td>
                </tr>
                <tr >
                    <th class="encabezado" colspan="5">
                        Concepto Raíz
                    </th>
                </tr>
                <tr >
                    <td  colspan="5">
                        {{ solicitud_cambio.concepto_path }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>

import EstatusLabelGlobal from "../../../globals/EstatusLabel";
export default {
    name: "TablaDatosSolicitudCambioPresupuesto",
    components: {EstatusLabelGlobal},
    props: ['solicitud_cambio'],
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
