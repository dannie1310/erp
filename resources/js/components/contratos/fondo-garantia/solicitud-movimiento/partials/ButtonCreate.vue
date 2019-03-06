<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_movimiento_bancario')" class="btn btn-app pull-right" >
            <i class="fa fa-plus"></i> Registrar Solicitud2

        </button>
        <solicitud-movimiento-fondo-garantia-modal-create></solicitud-movimiento-fondo-garantia-modal-create>
    </span>
</template>

<script>
    import SolicitudMovimientoFondoGarantiaModalCreate from "./ModalCreate";
    export default {
        name: "button-create",
        components: {SolicitudMovimientoFondoGarantiaModalCreate},
        methods: {
            init() {
                SolicitudMovimientoFondoGarantiaModalCreate.$emit('init');

            },
        }
    }
</script>

<style scoped>

</style>