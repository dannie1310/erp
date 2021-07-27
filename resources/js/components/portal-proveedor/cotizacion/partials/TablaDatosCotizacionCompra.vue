<template>
    <div class="row" v-if="cotizacion_compra">
        <div class="col-md-12">
            <span><i class="fa fa-comment-dollar"></i>Datos de Cotización de Compra</span>
            <table class="table table-bordered table-sm">
                <tr>
                    <th class="encabezado" colspan="4">
                        {{cotizacion_compra.empresa.razon_social}}
                    </th>
                </tr>
                <tr >
                    <th class="encabezado">
                        Sucursal
                    </th>
                    <th class="encabezado">
                        Folio de Cotización
                    </th>
                    <th class="encabezado">
                        Fecha de Cotización
                    </th>
                    <th class="encabezado">
                        Monto
                    </th>
                </tr>
                <tr>
                    <td>
                        {{cotizacion_compra.sucursal.descripcion}}
                    </td>
                    <td style="text-align: center">
                        <span style="color:black; text-decoration: underline">{{cotizacion_compra.numero_folio_format}}</span>
                    </td>
                    <td style="text-align: center">
                        {{cotizacion_compra.fecha_format}}
                    </td>
                     <td style="text-align: center">
                        {{cotizacion_compra.importe}}
                    </td>
                </tr>
                <template v-if="cotizacion_compra.observaciones!=''">
                    <tr>
                        <th colspan="5" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="5">
                            {{cotizacion_compra.observaciones}}
                        </td>
                    </tr>
                </template>
            </table>
        </div>
    </div>
</template>

<script>
import EncabezadoSolicitudCompra from "./Encabezado";
export default {
    name: "TablaDatosCotizacionCompra",
    components: {
        EncabezadoSolicitudCompra,
    },
    props: ['cotizacion_compra'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
