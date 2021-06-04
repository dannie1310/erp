<template>
    <span v-if="solicitud_cambio">
        <encabezado-solicitud-cambio-subcontrato v-bind:solicitud_cambio="solicitud_cambio"></encabezado-solicitud-cambio-subcontrato>
        <tabla-datos-solicitud-cambio-subcontrato v-bind:solicitud_cambio="solicitud_cambio"></tabla-datos-solicitud-cambio-subcontrato>
    </span>
</template>

<script>
import PDF from '../Formato';
import EstatusLabelGlobal from "../../../globals/EstatusLabel";
import EncabezadoSolicitudCambioSubcontrato from "./EncabezadoSolicitudCambioSubcontrato";
import TablaDatosSolicitudCambioSubcontrato from "./TablaDatosSolicitudCambioSubcontrato";
export default {
    name: "DatosSolicitud",
    components: {TablaDatosSolicitudCambioSubcontrato, EncabezadoSolicitudCambioSubcontrato, EstatusLabelGlobal, PDF},
    props: ['solicitud_cambio'],
}
</script>

<style scoped>

</style>
