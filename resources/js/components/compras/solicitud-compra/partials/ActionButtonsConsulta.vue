<template>
    <div class="btn-group">
        <Consulta v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_solicitud_compra')"/>
        <PDF v-bind:id="value.id" v-if="$root.can('consultar_solicitud_compra')"/>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_solicitud_compra') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>
<script>
    import Consulta from '../ShowModal';
    import PDF from '../FormatoSolicitudCompra.vue';
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "solicitud-compra-buttons",
        components: {ModalArchivos, PDF, Consulta},
        props: ['value'],
    }
</script>

<style scoped>

</style>
