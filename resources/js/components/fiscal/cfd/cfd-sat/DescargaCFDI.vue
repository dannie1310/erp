<template>
    <span>
        <button @click="descargar" class="btn btn-sm btn-outline-primary" title="Descargar">
            <i class="fa fa-download"></i>
        </button>
    </span>
</template>

<script>
export default {
    name: "DescargaCFDI",
    props: ['id'],
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
