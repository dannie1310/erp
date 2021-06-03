<template>
    <span v-if="subcontrato">
        <encabezado-subcontrato v-bind:subcontrato="subcontrato"></encabezado-subcontrato>
        <tabla-datos-subcontrato v-bind:subcontrato="subcontrato"></tabla-datos-subcontrato>
    </span>
</template>

<script>
import EncabezadoSubcontrato from "./EncabezadoSubcontrato";
import TablaDatosSubcontrato from "./TablaDatosSubcontrato";
export default {
    name: "DatosSubcontrato",
    props: ['subcontrato'],
    components: {TablaDatosSubcontrato, EncabezadoSubcontrato},
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
