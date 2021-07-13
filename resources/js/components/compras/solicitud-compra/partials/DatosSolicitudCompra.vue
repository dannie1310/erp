<template>
    <span>
        <encabezado-solicitud-compra v-bind:solicitud_compra="solicitud_compra"></encabezado-solicitud-compra>
        <tabla-datos-solicitud-compra v-bind:solicitud_compra="solicitud_compra"></tabla-datos-solicitud-compra>
    </span>

</template>

<script>
import ButtonsContratoProyectado from "./ActionButtons";
import EncabezadoSolicitudCompra from "./Encabezado";
import TablaDatosSolicitudCompra from "./TablaDatosSolicitudCompra";
export default {
    name: "DatosSolicitudCompra",
    components: {
        TablaDatosSolicitudCompra,
        EncabezadoSolicitudCompra,
        ButtonsContratoProyectado},
    props: ['solicitud_compra'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
