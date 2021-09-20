<template>
    <div class="row" v-if="invitacion">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos de Invitación a Cotizar</span>
            <table class="table  table-sm">

                <tr >
                    <th class="encabezado c120" >
                        Tipo de Invitación
                    </th>
                    <th class="encabezado c280" >
                      Proyecto
                    </th>
                    <th class="encabezado c120" >
                      Fecha de Cierre de Invitación
                    </th>
                    <th class="encabezado c120" v-if="invitacion.tipo_antecedente == 17">
                      Cotización Antecedente
                    </th>
                    <th class="encabezado c120" v-if="invitacion.tipo_antecedente == 49">
                      Presupuesto Antecedente
                    </th>

                </tr>
                <tr>
                    <td style="text-align: center">
                       {{invitacion.tipo_str}}
                    </td>
                    <td>
                        {{invitacion.descripcion_obra}}
                    </td>

                    <td style="text-align: center">
                        {{invitacion.fecha_cierre_format}}
                    </td>
                    <td style="text-align: center">
                        <span v-if="invitacion.folio_cotizacion_antecedente != ''">
                          {{invitacion.folio_cotizacion_antecedente}}
                        </span>
                        <span v-else-if="invitacion.folio_presupuesto_antecedente != ''">
                           {{invitacion.folio_presupuesto_antecedente}}
                        </span>
                        <span v-else >
                           No Aplica
                        </span>

                    </td>
                </tr>
                <tr>
                    <th style="border: none"></th>
                </tr>
                <tr v-if="invitacion.ubicacion_entrega_plataforma_digital != '' || invitacion.direccion_entrega != ''">
                    <th colspan="2" class="encabezado">
                        Dirección de Entrega
                    </th>
                    <th class="encabezado" colspan="2">
                        Enlace de Ubicación de Entrega
                    </th>
                </tr>
                <tr v-if="invitacion.ubicacion_entrega_plataforma_digital != '' || invitacion.direccion_entrega != ''">
                    <td colspan="2">
                        {{invitacion.direccion_entrega}}
                    </td>
                    <td colspan="2">
                        <a :href="invitacion.ubicacion_entrega_plataforma_digital" target="_blank" style="color: #1b1e21; text-decoration: underline">
                            {{invitacion.ubicacion_entrega_plataforma_digital}}</a>
                    </td>
                </tr>
                <tr>
                    <th style="border: none"></th>
                </tr>
                <tr v-if="invitacion.observaciones != ''">
                    <th colspan="4" class="encabezado">
                      Observaciones
                    </th>
                </tr>
                <tr>
                    <td v-if="invitacion.observaciones != ''" colspan="4">
                      {{invitacion.observaciones}}
                    </td>
                </tr>
                <tr>
                  <th style="border: none"></th>
                </tr>
                <tr>
                    <th colspan="4" class="encabezado">
                        Cuerpo del Correo Recibido
                    </th>
                </tr>
                <tr>
                    <td colspan="4" v-html="invitacion.cuerpo_correo">

                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import EncabezadoSolicitudCompra from "./Encabezado";

export default {
    name: "InvitacionCompraTablaCompletaDatos",
    components: {
        EncabezadoSolicitudCompra,
    },
    props: ['invitacion'],
    methods :{

    }
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
