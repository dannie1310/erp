<template>
    <div class="btn-group">
        <fondo-garantia-show :id="value.id" v-if="value.show"></fondo-garantia-show>
        <fondo-garantia-ajustar-saldo :id="value.id" v-if="value.ajustar_saldo"></fondo-garantia-ajustar-saldo>
        <solicitud-movimiento-fondo-garantia-create :objFondoGarantia="value.objFondoGarantia" :id="value.id" v-if="value.nueva_soliciud_movimiento" :tipo_boton="2"></solicitud-movimiento-fondo-garantia-create>
    </div>
</template>

<script>
    import FondoGarantiaShow from "../Show";
    import FondoGarantiaAjustarSaldo from "../AjustarSaldo";
    import SolicitudMovimientoFondoGarantiaCreate from "../solicitud-movimiento/Create";

    export default {
        name: "action-buttons",
        components: {FondoGarantiaShow, FondoGarantiaAjustarSaldo, SolicitudMovimientoFondoGarantiaCreate},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'fondo-garantia-show', params: {id: this.value.id}});
            },
            ajustar_saldo() {
                this.$router.push({name: 'fondo-garantia-ajustar-saldo', params: {id: this.value.id}});
            }
        }
    }
</script>