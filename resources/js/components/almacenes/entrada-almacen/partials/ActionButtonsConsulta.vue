<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_entrada_almacen')"/>
        <PDF v-bind:id="value.id"  v-if="$root.can('consultar_entrada_almacen')"></PDF>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    import Show from "../ShowModal";
    export default {
        name: "ActionButtons",
        components: {PDF, Show},
        props: ['value']
    }
</script>

<style scoped>

</style>
