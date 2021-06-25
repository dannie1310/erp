<template>
    <div class="btn-group">
        <Show v-bind:value="value"></Show>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>
<script>
import Show from '../ShowModal';
import ModalArchivos from "../../../globals/archivos/Modal";

    export default {
        name: "presupuesto-buttons",
        components: {Show, ModalArchivos},
        props: ['value'],
    }
</script>

<style scoped>

</style>
