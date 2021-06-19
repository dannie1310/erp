<template>
    <span>
        <button @click="asociar"  class="btn btn-app pull-right">
            <i class="fa fa-share-alt"></i> Asociar
        </button>
    </span>
</template>

<script>
    export default {
        name: "Asociar",
        props: ['datos_poliza'],
        methods : {
            asociar() {
                return this.$store.dispatch('contabilidad/poliza/asociarCFDI', {
                    data: this.datos_poliza
                }).then((data) => {
                    this.$emit('created', data)
                });
            },
        }
    }
</script>

<style scoped>

</style>
