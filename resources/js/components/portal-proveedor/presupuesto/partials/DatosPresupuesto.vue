<template>
    <span v-if="presupuesto">
        <encabezado-presupuesto v-bind:presupuesto="presupuesto" />
        <tabla-datos-presupuesto v-bind:presupuesto="presupuesto" />
    </span>
</template>

<script>

import EncabezadoPresupuesto from "./EncabezadoPresupuesto";
import TablaDatosPresupuesto from "./TablaDatosPresupuesto";
export default {
    name: "DatosPresupuesto",
    components: {TablaDatosPresupuesto, EncabezadoPresupuesto},
    props: ['presupuesto'],
}
</script>

<style scoped>

</style>
