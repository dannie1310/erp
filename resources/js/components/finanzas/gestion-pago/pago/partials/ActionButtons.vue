<template>
    <div class="btn-group">
        <VerPago v-if="value.show" v-bind:id="value.id" />
        <DatosPago v-if="value.pagar" v-bind:id="value.id" />
        <Delete v-if="value.delete" v-bind:id="value.id" />
        <Relaciones v-bind:transaccion="value.transaccion"/>
    </div>
</template>

<script>
    import DatosPago from "../DatosPago";
    import Delete from "../Delete";
    import Relaciones from "../../../../globals/ModalRelaciones";
    import VerPago from "../Show";
    export default {
        name: "action-buttons",
        components: {Relaciones, Delete, DatosPago, VerPago},
        props: ['value']
    }
</script>

<style scoped>

</style>
