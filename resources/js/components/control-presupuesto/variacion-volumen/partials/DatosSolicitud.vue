<template>
    <span v-if="solicitud_cambio">
        <encabezado-solicitud-cambio-presupuesto v-bind:solicitud_cambio="solicitud_cambio"></encabezado-solicitud-cambio-presupuesto>
        <tabla-datos-solicitud-cambio-presupuesto v-bind:solicitud_cambio="solicitud_cambio"></tabla-datos-solicitud-cambio-presupuesto>
    </span>
</template>

<script>
import EncabezadoSolicitudCambioPresupuesto from "./EncabezadoSolicitudCambioPresupuesto";
import TablaDatosSolicitudCambioPresupuesto from "./TablaDatosSolicitudCambioPresupuesto";

export default {
    name: "DatosSolicitud",
    components: {
        TablaDatosSolicitudCambioPresupuesto,
        EncabezadoSolicitudCambioPresupuesto,
    },
    props: ['solicitud_cambio'],
}
</script>

<style scoped>

</style>
