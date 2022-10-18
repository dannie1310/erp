<template>
    <span>
        <button @click="descargar()" type="button" class="btn btn-app btn-outline-success pull-right float-right" :disabled="cargando" title="Descargar Layout">
            <i class="fa fa-download"></i>Descargar Excel
        </button>
    </span>
</template>

<script>
    export default {
        name: "descarga-layout",
        props: ['query'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/cfdi-sat/descargaLayout',
                {
                    params: this.query,
                })
                .then(() => {
                    this.$emit('success')
                    this.cargando = false;
                })
            }
        },
    }
</script>

<style scoped>

</style>
