<template>
    <div class="btn-group">
    <Delete v-if="value.delete && value.estado == 0" v-bind:id="value.id" v-bind:pagina="value.pagina"/>
    <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_formato_entrada_almacen')"></PDF>

    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    import Delete from "../Delete";
    export default {
        name: "ActionButtons",
        components: {Delete, PDF},
        props: ['value']
    }
</script>

<style scoped>

</style>
