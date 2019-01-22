<template>
    <button class="btn btn-app btn-info pull-right" @click="checkCuentas" v-if="$root.can('ingresar_cuenta_faltante_movimiento_prepoliza')">
        <i class="fa fa-dollar"></i> Ingresar cuentas faltantes
    </button>
</template>

<script>
    export default {
        name: "poliza-ingresar-cuentas",
        props:['original', 'poliza'],
        methods: {
            checkCuentas() {

            }
        }
    }
</script>

<style scoped>

</style>