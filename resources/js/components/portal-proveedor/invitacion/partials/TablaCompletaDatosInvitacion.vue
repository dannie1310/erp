<template>
    <div class="row" v-if="invitacion">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos de Invitación a Cotizar</span>
            <table class="table  table-sm">
                <tr>
                    <th class="encabezado c120" v-if="invitacion.tipo_antecedente == 17">
                        Solicitud de Compra
                    </th>
                    <th class="encabezado c120" v-if="invitacion.tipo_antecedente == 49">
                        Contrato
                    </th>
                    <th class="encabezado c280" >
                      Proyecto
                    </th>
                    <th class="encabezado c120" >
                      Fecha de Cierre de Invitación
                    </th>
                    <th colspan="2" class="encabezado">
                      Observaciones
                    </th>
                </tr>
                <tr>
                    <td style="text-align: center">
                        {{invitacion.transaccion.numero_folio_format}}
                    </td>
                    <td>
                        {{invitacion.descripcion_obra}}
                    </td>

                    <td style="text-align: center">
                        {{invitacion.fecha_cierre_format}}
                    </td>
                    <td >
                        {{invitacion.observaciones}}
                    </td>
                </tr>
                <tr>
                    <th style="border: none"></th>
                </tr>
                <tr>
                    <th colspan="3" class="encabezado">
                        Dirección de Entrega
                    </th>
                    <th class="encabezado">
                        Enlace de Ubicación de Entrega
                    </th>

                </tr>
                <tr>
                    <td colspan="3">
                        {{invitacion.direccion_entrega}}
                    </td>
                    <td>
                        <a :href="invitacion.ubicacion_entrega_plataforma_digital" target="_blank" style="color: #1b1e21; text-decoration: underline">
                            {{invitacion.ubicacion_entrega_plataforma_digital}}</a>
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
