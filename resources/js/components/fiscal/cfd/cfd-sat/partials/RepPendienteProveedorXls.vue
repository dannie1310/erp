<template>
    <button @click="descargar()" type="button" class="btn btn-sm btn-outline-primary" :disabled="cargando" title="Descargar REP Pendiente Proveedor">
        <i class="fa fa-download"></i>
    </button>
</template>

<script>
    export default {
        name: "RepPendienteProveedorXls",
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/proveedor-rep/repPendienteProveedorXls',
                {
                    params: { id:this.id },
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
