<template>
    <div class="btn-group">
        <router-link :to="{ name: 'pago-show', params: {id: this.value.id}}" v-if="$root.can('consultar_pagos')" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <DatosPago v-if="value.pagar" v-bind:id="value.id" />
        <Delete v-if="value.delete" v-bind:id="value.id" />
        <Relaciones v-bind:transaccion="value.transaccion"/>
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
