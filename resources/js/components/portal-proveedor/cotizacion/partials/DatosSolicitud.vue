<template>
    <span>
        <encabezado-solicitud v-bind:solicitud_compra="solicitud"></encabezado-solicitud>
        <tabla-datos-solicitud v-bind:solicitud_compra="solicitud"></tabla-datos-solicitud>
    </span>
</template>

<script>
    import EncabezadoSolicitud from "./Encabezado";
    import TablaDatosSolicitud from "./TablaDatosSolicitud";
    export default {
        name: "DatosSolicitud",
        components: {
            TablaDatosSolicitud,
            EncabezadoSolicitud
        },
        props: ['solicitud'],
    }
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
