<template>
    <span v-if="contrato_proyectado && contrato_proyectado.tipo_transaccion == 49">
        <encabezado-contrato-proyectado v-bind:contrato_proyectado="contrato_proyectado"></encabezado-contrato-proyectado>
        <tabla-datos-contrato-proyectado v-bind:contrato_proyectado="contrato_proyectado"></tabla-datos-contrato-proyectado>
    </span>

</template>

<script>
import EncabezadoContratoProyectado from "../partials/EncabezadoPresupuesto.vue";
import TablaDatosContratoProyectado from "../partials/TablaDatosContratoProyectado.vue";
export default {
    name: "DatosContratoProyectado",
    components: {EncabezadoContratoProyectado, TablaDatosContratoProyectado},
    props: ['contrato_proyectado'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
