<template>
    <span v-if="presupuesto">
        <encabezado-presupuesto v-bind:presupuesto="presupuesto"></encabezado-presupuesto>
        <tabla-datos-presupuesto v-bind:presupuesto="presupuesto"></tabla-datos-presupuesto>
    </span>
</template>

<script>

import PresupuestoButtons from "./ActionButtons";
import EncabezadoPresupuesto from "./EncabezadoPresupuesto";
import TablaDatosPresupuesto from "./TablaDatosPresupuesto";
export default {
    name: "DatosPresupuesto",
    components: {TablaDatosPresupuesto, EncabezadoPresupuesto, PresupuestoButtons},
    props: ['presupuesto'],
}
</script>

<style scoped>

</style>
