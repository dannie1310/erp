<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <Documento v-if="invitacion.carta_terminos" v-bind:url="url" v-bind:id="invitacion.carta_terminos.id" v-bind:descripcion="invitacion.carta_terminos.tipo_archivo.descripcion" v-bind:texto="invitacion.carta_terminos.tipo_archivo.descripcion" ></Documento>
                <DescargaDocumento v-if="invitacion.formato_cotizacion" v-bind:url="url_descarga" v-bind:id="invitacion.formato_cotizacion.id" v-bind:descripcion="invitacion.formato_cotizacion.tipo_archivo.descripcion" v-bind:texto="invitacion.formato_cotizacion.tipo_archivo.descripcion"></DescargaDocumento>
                <formato-solicitud-compra v-if="invitacion.solicitud_compra"  v-bind:id="invitacion.solicitud_compra.id" v-bind:db="invitacion.base_datos" v-bind:id_obra="invitacion.id_obra" v-bind:texto="'Solicitud de Compra'"></formato-solicitud-compra>
            </div>
        </div>
    </span>
</template>

<script>

    import InvitacionCompraEncabezado from "../../../compras/invitacion/partials/Encabezado";
    import InvitacionCompraTablaCompletaDatos from "../../../compras/invitacion/partials/TablaCompletaDatosInvitacion";
    import Documento from "../../../globals/archivos/Documento";
    import DescargaDocumento from "../../../globals/archivos/DescargaDocumento";
    import FormatoSolicitudCompra from "../../../compras/solicitud-compra/FormatoSolicitudCompra";
    export default {
        name: "DocumentosInvitacion",
        components: {
            FormatoSolicitudCompra,
            DescargaDocumento,
            Documento,
            InvitacionCompraTablaCompletaDatos,
            InvitacionCompraEncabezado
        },
        props: ['invitacion'],
        data(){
            return{
                cargando: false,
                url : '/api/padron-proveedores/archivo-invitacion/{id}/documento?access_token='+this.$session.get('jwt'),
                url_descarga : '/api/padron-proveedores/archivo-invitacion/{id}/documento/descargar?access_token='+this.$session.get('jwt'),
            }
        },
    }
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
