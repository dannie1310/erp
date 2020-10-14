<template>
    <div class="btn-group">
        <Show  v-if="$root.can('consultar_salida_almacen')" v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" />
        <PDF v-bind:id="value.id"  v-if="$root.can('consultar_salida_almacen')"></PDF>
    </div>
</template>

<script>
    import PDF from "../FormatoSalidaAlmacen";
    import Show from "../ShowModal";

    export default {
        name: "action-buttons",
        components: {Show,PDF},
        props: ['value']
    }

</script>
