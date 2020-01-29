<template>
    <span>
        <div class="btn-group">
            <FacturaShow v-if="value.show" v-bind:id="value.id" />
            <PDF v-bind:id="value.id" @click="value.id" ></PDF>
            <Eliminar  v-if="$root.can('eliminar_factura')" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    import FacturaShow from "../Show";
    import Eliminar from '../Eliminar';
    export default {
        name: "action-buttons",
        components: {PDF, FacturaShow, Eliminar},
        props: ['value'],
    }
</script>

<style scoped>

</style>