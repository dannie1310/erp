<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show" v-bind:pagina="value.pagina"/>
        <Delete v-if="value.delete && value.estado == 0" v-bind:id="value.id" v-bind:pagina="value.pagina"/>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_entrada_almacen')"></PDF>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    import Delete from "../Delete";
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {Delete, PDF, Show},
        props: ['value']
    }
</script>

<style scoped>

</style>
