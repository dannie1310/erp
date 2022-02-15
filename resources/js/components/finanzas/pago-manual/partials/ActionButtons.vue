<template>
    <div class="btn-group ">
        <!-- <FacturaAplicar v-if="value.aplicar" v-bind:id_empresa="value.id_empresa" v-bind:pago="value.pago" v-if="$root.can('consultar_pagos')"/> -->
        <router-link :to="{ name: 'pago-aplicar', params: {id: this.value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Aplicar">
            <i class="fa fa-check"></i>
        </router-link>
    </div>
</template>

<script>
    import FacturaAplicar from "../FacturaAplicarModal";
    export default {
        name: "action-buttons",
        components: { FacturaAplicar },
        props: ['value']
    }
</script>