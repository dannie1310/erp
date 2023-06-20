<template>
    <span>
        <button @click="descargar()" type="button" class="btn btn-app btn-outline-success pull-right" :disabled="cargando" title="Descargar Layout">
            <i class="fa fa-download"></i>Descargar Excel
        </button>
    </span>
</template>

<script>
    export default {
        name: "descarga-layout",
        props : ['datos'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                console.log("AQUI? LLEGA?? ", this.datos);
               // this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/contabilidad-electronica/descargar', {datos: this.datos})
                    .then(() => {
                        this.$emit('success')
                      //  this.cargando = false;
                    })
                }
        },
    }
</script>

<style scoped>

</style>
