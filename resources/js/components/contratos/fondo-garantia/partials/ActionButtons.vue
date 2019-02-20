<template>
    <div class="btn-group">
        <fondo-garantia-show :id="value.id" v-if="value.show"></fondo-garantia-show>

    </div>
</template>

<script>
    import FondoGarantiaShow from "../Show";

    export default {
        name: "action-buttons",
        components: {FondoGarantiaShow},
        props: ['value'],
        methods: {

            show() {
                this.$router.push({name: 'fondo-garantia-show', params: {id: this.value.id}});
            }
        }
    }
</script>