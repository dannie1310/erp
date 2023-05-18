<template>
    <input type="checkbox"  :id="`es_ganador${participante.id}`" :checked="participante.es_ganador" v-on:change="update" :disabled="actualizando">
</template>

<script>
export default {
    name: "CheckEsGanador",
    props: ['id','participante'],
    data() {
        return {
            participante_edit :''
        }
    },
    mounted() {
        this.participante_edit = this.participante;
    },
    methods: {
        update(){
            this.$store.commit('concursos/concurso/SET_ACTUALIZANDO', true);
            return this.$store.dispatch('concursos/concurso/setGanadorDirecto', {

                id: this.id,
                id_participante: this.participante_edit.id,
                data: {},
            })
            .then(data => {
                this.$store.commit('concursos/concurso/SET_CONCURSO', data);
            })
            .catch(error => {
            })
            .finally(() => {
                this.$store.commit('concursos/concurso/SET_ACTUALIZANDO', false);
            })
        },
    },
    computed: {
        actualizando() {
            return this.$store.getters['concursos/concurso/actualizando'];
        },
    }
}
</script>

<style scoped>

</style>
