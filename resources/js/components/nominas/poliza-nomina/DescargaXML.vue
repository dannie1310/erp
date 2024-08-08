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
        props: ['id', 'id_empresa'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                console.log(this.id_empresa)
                return this.$store.dispatch('nominas/poliza-contpaq/descargaXML', {id: this.id, id_empresa: this.id_empresa})
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
