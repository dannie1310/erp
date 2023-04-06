<template>
    <span>
        <button @click="descargar" class="btn btn-sm btn-outline-primary" title="Descargar" v-if="txt!=1" :disabled="cargandoEstado">
            <i class="fa fa-download"></i>
        </button>
        <button @click="descargar" class="btn btn-sm btn-primary" title="Descargar"  :disabled="cargandoEstado" v-else>
            <i class="fa fa-download"></i> Descargar XML
        </button>
    </span>
</template>

<script>
export default {
    name: "DescargaCFDI",
    props: ['id','txt'],
    computed: {
        cargandoEstado(){
            return this.$store.getters['fiscal/cfd-sat/currentEstado'];
        }
    },
    methods:{
        descargar(){
            this.descargando = true;
            return this.$store.dispatch('fiscal/cfd-sat/descargarIndividual',
                {
                    id: this.id,
                })
                .then(data => {
                    this.$emit('success');
                }).finally(() => {
                    this.descargando = false;
                });
        },
    }
}
</script>

<style scoped>

</style>
