<template>
    <div class="btn-group">
        <PDF v-bind:id="value.id" ></PDF>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'"></ModalArchivos>
    </div>
</template>

<script>

    import PDF from './FormatoOrdenCompra';
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "action-buttons",
        components: {PDF, ModalArchivos},
        props: ['value'],

    }
</script>
