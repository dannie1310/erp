<template>
    <div class="row" v-if="invitacion">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos de Invitación a Cotizar</span>
            <table class="table table-bordered table-sm">
                <tr>
                    <th class="encabezado c120" rowspan="2">
                        Solicitud de Compra
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

                    <td style="text-align: center">
                        <router-link :to="{name: 'solicitud-show', params:{id : invitacion.solicitud_compra.id }}" target="_blank">
                            <span style="color:black; text-decoration: underline">{{invitacion.solicitud_compra.numero_folio_format}}</span>
                        </router-link>
                    </td>
                    <td>
                        {{invitacion.empresa.razon_social}}
                    </td>
                    <td >
                        {{invitacion.sucursal.descripcion}}
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
                <template v-if="invitacion.observaciones!='...'">
                    <tr>
                        <th colspan="6" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="6">
                            {{invitacion.observaciones}}
                        </td>
                    </tr>
                </template>
            </table>
        </div>
    </div>
</template>

<script>
import EncabezadoSolicitudCompra from "./Encabezado";
/*import EncabezadoContratoProyectado from "../Encabezado";*/
export default {
    name: "InvitacionCompraTablaDatos",
    components: {
        EncabezadoSolicitudCompra,
        /*EncabezadoContratoProyectado,*/ },
    props: ['invitacion'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
