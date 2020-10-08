<template>
    <span>
        <div class="btn-group">
            <FacturaShow v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_factura')" />
            <PDF v-bind:id="value.id" @click="value.id" v-if="$root.can('consultar_factura')" ></PDF>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    import FacturaShow from "../ShowModal";
    export default {
        name: "action-buttons",
        components: {PDF,FacturaShow},
        props: ['value'],
    }
</script>

<style scoped>

</style>
