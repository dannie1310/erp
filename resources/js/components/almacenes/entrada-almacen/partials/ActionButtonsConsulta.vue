<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_entrada_almacen')"/>
        <PDF v-bind:id="value.id"  v-if="$root.can('consultar_entrada_almacen')"></PDF>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_entrada_almacen') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    import Show from "../ShowModal";
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "ActionButtons",
        components: {PDF, Show, ModalArchivos},
        props: ['value']
    }
</script>

<style scoped>

</style>
