<template>
    <span>
        <button @click="init" v-if="$root.can('solicitar_baja_cuenta_bancaria_empresa')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Inventario
        </button>
    </span>
</template>

<script>
    export default {
        name: "inventario-fisico-create"
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>