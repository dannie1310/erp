<template>
    <div class="btn-group">
        <fondo-garantia-ajustar-saldo :id="value.id" v-if="value.ajustar_saldo"></fondo-garantia-ajustar-saldo>
    </div>
</template>

<script>
    import FondoGarantiaAjustarSaldo from "../AjustarSaldo";

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