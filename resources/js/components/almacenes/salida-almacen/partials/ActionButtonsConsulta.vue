<template>
    <div class="btn-group">
        <Show  v-if="$root.can('consultar_salida_almacen')" v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" />
        <PDF v-bind:id="value.id"  v-if="$root.can('consultar_salida_almacen')"></PDF>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'"></ModalArchivos>
    </div>
</template>

<script>
    import PDF from "../FormatoSalidaAlmacen";
    import Show from "../ShowModal";
    import ModalArchivos from "../../../globals/archivos/Modal";

    export default {
        name: "action-buttons",
        components: {Show,PDF, ModalArchivos},
        props: ['value']
    }

</script>
