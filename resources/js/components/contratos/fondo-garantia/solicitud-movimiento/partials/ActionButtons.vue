<template>
    <div class="btn-group">
        <solicitud-movimiento-fondo-garantia-show :id="value.id" v-if="value.show"></solicitud-movimiento-fondo-garantia-show>
    </div>
</template>

<script>
    import SolicitudMovimientoFondoGarantiaShow from "../Show";
    /*import MovimientoBancarioEdit from "../Edit";*/
    export default {
        name: "action-buttons-smfg",
        components: {SolicitudMovimientoFondoGarantiaShow},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'solicitud-movimiento-fondo-garantia-show', params: {id: this.value.id}});
            }
        }
    }
</script>