<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show" v-bind:pagina="value.pagina"/>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_entrada_almacen')"></PDF>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {PDF, Show},
        props: ['value']
    }
</script>

<style scoped>

</style>
