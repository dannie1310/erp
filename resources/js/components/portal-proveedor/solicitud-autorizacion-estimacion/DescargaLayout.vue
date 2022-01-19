<template>
    <span>
        <button @click="descargar()" type="button" class="btn btn-outline-success" :disabled="cargando" title="Descargar Layout">
            <i class="fa fa-download" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
            Descargar
        </button>
    </span>
</template>

<script>
    export default {
        name: "descarga-layout",
        props: ['id', 'subcontrato'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('portalProveedor/solicitud-autorizacion-avance/descargaLayout', {id: this.id, base:this.subcontrato.base})
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
