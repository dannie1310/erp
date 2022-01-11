<template>
    <span>
        <subcontrato-encabezado v-bind:subcontrato="subcontrato" />
        <subcontrato-tabla-completa-datos v-bind:subcontrato="subcontrato" />
    </span>
</template>

<script>
    import SubcontratoEncabezado from "./EncabezadoSubcontrato";
    import SubcontratoTablaCompletaDatos from "./TablaCompletaDatosSubcontrato";
    export default {
        name: "DatosSubcontrato",
        components: {
            SubcontratoEncabezado,SubcontratoTablaCompletaDatos
        },
        props: ['subcontrato'],
    }
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }

</style>
