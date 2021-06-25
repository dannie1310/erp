<template>
    <div class="btn-group">
        <Show v-bind:value="value" v-if="$root.can('consultar_subcontrato')"></Show>
        <PDF v-bind:id="value.id" @click="value.id" v-if="$root.can('consultar_subcontrato')"></PDF>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_subcontrato') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>

<script>
    import Show from '../ShowModal'
    import ModalArchivos from "../../../globals/archivos/Modal";
    import PDF from '../FormatoSubcontrato';
    export default {
        name: "ActionButtons",
        components: {Show, ModalArchivos, PDF},
        props: ['value'],
    }
</script>
