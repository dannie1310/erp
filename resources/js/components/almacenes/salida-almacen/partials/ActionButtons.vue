<template>
    <div class="btn-group">
        <EliminarSalida v-bind:id="value.id" v-bind:pagina="value.pagina"/>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_salida_almacen')"></PDF>
    </div>
</template>

<script>
    import EliminarSalida from "../delete";
    import PDF from "../FormatoSalidaAlmacen";

    export default {
        name: "action-buttons",
        components: {EliminarSalida, PDF},
        props: ['value']
    }

</script>
