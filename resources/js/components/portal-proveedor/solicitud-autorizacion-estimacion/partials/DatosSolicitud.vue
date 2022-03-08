<template>
    <span>
        <encabezado v-bind:estimacion="estimacion"></encabezado>
        <tabla-datos v-bind:estimacion="estimacion"></tabla-datos>
    </span>

</template>

<script>
import Encabezado from "./EncabezadoSolicitud";
import TablaDatos from "./TablaDatosSolicitud";
export default {
    name: "DatosSolicitud",
    components: {
        TablaDatos,
        Encabezado
    },
    props: ['estimacion'],
}
</script>
