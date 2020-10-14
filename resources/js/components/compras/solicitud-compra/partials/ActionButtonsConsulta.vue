<template>
    <div class="btn-group">
        <Consulta v-bind:id="value.id" v-bind:numero_folio="value.numero_folio"/>
        <PDF v-bind:id="value.id"/>
    </div>
</template>
<script>
    import Consulta from '../ShowModal';
    import PDF from '../FormatoSolicitudCompra.vue';
    export default {
        name: "solicitud-compra-buttons",
        components: {PDF, Consulta},
        props: ['value'],
    }
</script>

<style scoped>

</style>
