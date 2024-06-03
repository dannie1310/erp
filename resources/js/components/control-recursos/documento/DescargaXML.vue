<template>
    <span>
        <button @click="descargar()" type="button"  class="btn btn-sm btn-outline-success" title="Descargar XML">
            <i class="fa fa-download"></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "descarga-xml",
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('controlRecursos/documento/descargaXML', {id: this.id})
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
