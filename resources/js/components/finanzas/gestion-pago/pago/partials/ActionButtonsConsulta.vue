<template>
    <div class="btn-group">
        <Consulta v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_pagos')"/>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_pagos') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>

<script>
    import Consulta from '../ShowModal';
    import ModalArchivos from "../../../../globals/archivos/Modal";
    export default {
        name: "action-buttons",
        components: {Consulta, ModalArchivos},
        props: ['value']
    }
</script>

<style scoped>

</style>
