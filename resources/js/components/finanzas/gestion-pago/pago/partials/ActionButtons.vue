<template>
    <div class="btn-group">
        <DatosPago v-if="value.pagar" v-bind:id="value.id" />
        <Delete v-if="value.delete" v-bind:id="value.id" />
        <Relaciones v-bind:relaciones="value.relaciones"/>
    </div>
</template>

<script>
    import DatosPago from "../DatosPago";
    import Delete from "../Delete";
    import Relaciones from "../../../../globals/ModalRelaciones";
    export default {
        name: "action-buttons",
        components: {Relaciones, Delete, DatosPago},
        props: ['value']
    }
</script>

<style scoped>

</style>
