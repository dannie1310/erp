<template>
    <span>
        <button @click="descargar()" type="button" class="btn btn-app pull-right float-right" :disabled="cargando||cargando_padre" title="Descargar Layout">
            <i class="fa fa-download"></i>Descargar Excel
        </button>
    </span>
</template>

<script>
    export default {
        name: "PolizasEgresoSinCFDIXls",
        props: ['query','cargando_padre'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/poliza-cfdi/polizasEgresoSinCFDIXls',
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
