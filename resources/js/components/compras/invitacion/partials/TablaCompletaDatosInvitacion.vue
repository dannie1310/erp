<template>
    <div class="row" v-if="invitacion">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos de Invitación a Cotizar</span>
            <table class="table  table-sm">
                <tr>
                    <th class="encabezado c120" rowspan="2" v-if="invitacion.tipo_antecedente == 17">
                        Solicitud de Compra
                    </th>
                    <th class="encabezado c120" rowspan="2" v-if="invitacion.tipo_antecedente == 49">
                        Contrato Proyectado
                    </th>
                    <th class="encabezado" colspan="4">
                        Proveedor Invitado
                    </th>

                    <th class="encabezado c120" rowspan="2">
                        Fecha de Cierre de Invitación
                    </th>
                </tr>
                <tr >

                    <th class="encabezado">
                        Razón Social
                    </th>
                    <th class="encabezado">
                        Sucursal
                    </th>
                    <th class="encabezado">
                        Contacto
                    </th>
                    <th class="encabezado">
                        Correo
                    </th>

                </tr>
                <tr>
                    <td style="text-align: center" v-if="this.$session.get('db')">
                        <router-link :to="{name: 'solicitud-show', params:{id : invitacion.solicitud_compra.id }}" target="_blank">
                            <span style="color:black; text-decoration: underline">{{invitacion.solicitud_compra.numero_folio_format}}</span>
                        </router-link>
                    </td>
                  <td style="text-align: center" v-else>
                     {{invitacion.solicitud_compra.numero_folio_format}}
                  </td>
                    <td v-if="invitacion.empresa">
                        {{invitacion.empresa.razon_social}}
                    </td>
                    <td v-else>
                        N/A
                    </td>
                    <td v-if="invitacion.sucursal">
                        {{invitacion.sucursal.descripcion}}
                    </td>
                    <td v-else>
                        N/A
                    </td>
                    <td >
                        {{invitacion.nombre_contacto}}
                    </td>
                     <td >
                        {{invitacion.email}}
                    </td>
                    <td style="text-align: center">
                        {{invitacion.fecha_cierre_format}}
                    </td>
                </tr>
                <tr>
                    <th style="border: none"></th>
                </tr>
                <tr>
                    <th colspan="2" class="encabezado">
                        Dirección de Entrega
                    </th>
                    <th colspan="2" class="encabezado">
                        Enlace de Ubicación de Entrega
                    </th>
                    <th colspan="2" class="encabezado">
                        Observaciones
                    </th>
                </tr>
                <tr>
                    <td colspan="2">
                        {{invitacion.direccion_entrega}}
                    </td>
                    <td colspan="2">
                        <a :href="invitacion.ubicacion_entrega_plataforma_digital" target="_blank" style="color: #1b1e21; text-decoration: underline">
                            {{invitacion.ubicacion_entrega_plataforma_digital}}</a>
                    </td>
                    <td colspan="2">
                        {{invitacion.observaciones}}
                    </td>
                </tr>
                <tr>
                    <th style="border: none"></th>
                </tr>
                <tr>
                    <th colspan="6" class="encabezado">
                        Cuerpo del Correo Enviado
                    </th>
                </tr>
                <tr>
                    <td colspan="6" v-html="invitacion.cuerpo_correo">

                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "InvitacionCompraTablaCompletaDatos",
    components: { },
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
