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
        props: ['id', 'id_empresa', 'bd_empresa'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('nominas/poliza-contpaq/descargaXML', {id: this.id, id_empresa: this.id_empresa})
                    .then(() => {
                        return this.$store.dispatch('nominas/poliza-contpaq/paginate',
                            {
                                params: {sort:'fechapoliza',order:'desc', scope: 'conGuid', id_empresa: this.id_empresa, bd_empresa: this.bd_empresa},
                            })
                            .then(data => {
                                this.$store.commit('nominas/poliza-contpaq/SET_POLIZAS', data.data);
                                this.$store.commit('nominas/poliza-contpaq/SET_META', data.meta);
                            })
                        this.$emit('success')
                    })
            }
        },
    }
</script>

<style scoped>

</style>
