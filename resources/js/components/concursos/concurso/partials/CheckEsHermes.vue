<template>
    <input type="checkbox" id="es_empresa_hermes" :id="`switch${participante.id}`" v-model="participante.es_empresa_hermes" @change="update" >
</template>

<script>
export default {
    name: "CheckEsHermes",
    props: ['id','participante'],
    data() {
        return {
        }
    },

    methods: {
        update(){
            return this.$store.dispatch('concursos/concurso/updateParticipanteDirecto', {

                id: this.id,
                id_participante: this.participante.id,
                data: this.participante,
            })
            .then(data => {
                this.$store.commit('concursos/concurso/SET_CONCURSO', data);
            })
            .catch(error => {
            })
            .finally(() => {
            })
        },
    },
}
</script>

<style scoped>

</style>
