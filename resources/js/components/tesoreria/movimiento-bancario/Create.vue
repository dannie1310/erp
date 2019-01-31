<template>
    <a class="btn btn-app">
        <i class="fa fa-plus"></i> Registrar Movimiento
    </a>
</template>

<script>
    export default {
        name: "movimiento-bancario-create",

        mounted() {
            this.getTiposMovimiento()
            this.getCuentas()
        },
        methods: {
            getTiposMovimiento() {
                return this.$store.dispatch('tesoreria/tipo-movimiento/fetch');
            },

            getCuentas() {
                return this.$store.dispatch('cadeco/cuenta/fetch', {
                    scope: 'paraTraspaso'
                })
            }
        },

        computed: {
            tiposMovimiento() {
                return this.$store.getters['tesoreria/tipo-movimiento/tipos']
            },

            cuentas() {
                return this.$store.getters['cadeco/cuenta/cuentas']
            }
        }
    }
</script>

<style scoped>

</style>