<template>
    <span>
        <encabezado v-bind:solicitud="solicitud" />
        <tabla-datos v-bind:solicitud="solicitud" />
    </span>

</template>

<script>
import Encabezado from "./Encabezado";
import TablaDatos from "./TablaDatosDocumento";
export default {
    name: "Datos",
    components: {
        Encabezado,TablaDatos
    },
    props: ['solicitud'],
}
</script>
