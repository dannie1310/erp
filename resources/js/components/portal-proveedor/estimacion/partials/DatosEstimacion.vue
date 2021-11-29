<template>
    <span>
        <encabezado v-bind:estimacion="estimacion"></encabezado>
        <tabla-datos v-bind:estimacion="estimacion"></tabla-datos>
    </span>

</template>

<script>
import Encabezado from "./EncabezadoEstimacion";
import TablaDatos from "./TablaDatosEstimacion";
export default {
    name: "DatosEstimacion",
    components: {
        TablaDatos,
        Encabezado
    },
    props: ['estimacion'],
}
</script>
