<template>
    <span>
        <div class="btn-group">
            <FacturaShow v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_factura')" />
            <PDF v-bind:id="value.id" @click="value.id" v-if="$root.can('consultar_factura')" ></PDF>
            <CFDI v-bind:id="value.id" @click="value.id" ></CFDI>
            <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_factura') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    import FacturaShow from "../ShowModal";
    import ModalArchivos from "../../../globals/archivos/Modal";
    import CFDI from "../CFDI";
    export default {
        name: "action-buttons",
        components: {PDF,FacturaShow, ModalArchivos, CFDI},
        props: ['value'],
    }
</script>

<style scoped>

</style>
