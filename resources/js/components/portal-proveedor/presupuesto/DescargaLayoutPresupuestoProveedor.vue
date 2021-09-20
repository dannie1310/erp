<template>
    <span>
        <button @click="descargar()" type="button" class="btn btn-sm btn-outline-primary" :disabled="cargando" title="Descargar Layout">
            <i class="fa fa-download" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "descarga-layout-proveedor",
        props: ['id','id_presupuesto'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('contratos/presupuesto/descargaLayoutProveedor', {id: this.id,id_presupuesto:this.id_presupuesto})
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
