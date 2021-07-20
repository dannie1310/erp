<template>
    <div class="row" v-if="solicitud_compra">
        <div class="col-md-12">
            <span><i class="fa fa-comment-dots"></i>Datos de Solicitud de Compra</span>
            <table class="table table-bordered table-sm">
                <tr >
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
                        Usuario Registró
                    </th>
                    <th class="encabezado">
                        Fecha de Registro
                    </th>
                </tr>
                <tr>
                    <td>
                        {{solicitud_compra.area_compradora}}
                    </td>
                    <td style="text-align: center">
                        <router-link :to="{name: 'solicitud-show', params:{id : solicitud_compra.id }}" target="_blank">
                            <span style="color:black; text-decoration: underline">{{solicitud_compra.numero_folio_format}}</span>
                        </router-link>
                    </td>
                    <td style="text-align: center">
                        {{solicitud_compra.fecha_format}}
                    </td>
                    <td >
                        {{solicitud_compra.usuario.nombre}}
                    </td>
                     <td style="text-align: center">
                        {{solicitud_compra.fecha_registro}}
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
import ButtonsContratoProyectado from "./ActionButtons";
import EncabezadoSolicitudCompra from "./Encabezado";
/*import EncabezadoContratoProyectado from "../Encabezado";*/
export default {
    name: "TablaDatosSolicitudCompra",
    components: {
        EncabezadoSolicitudCompra,
        /*EncabezadoContratoProyectado,*/ ButtonsContratoProyectado},
    props: ['solicitud_compra'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
