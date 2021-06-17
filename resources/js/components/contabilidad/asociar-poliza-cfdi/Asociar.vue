<template>
    <span>
        <button @click="asociar" v-if="$root.can('registrar_empresa')" class="btn btn-app pull-right">
            <i class="fa fa-plus"></i> Asociar CFDI
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
