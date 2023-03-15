<template>
    <span>
        <button @click="cerrar"  type="button" class="btn"
                :class="texto == undefined?'btn-sm btn-outline-danger':'btn-danger'" title="Cerrar Concurso"><i class="fa fa-lock"></i>
            <span v-if="texto">{{texto}}</span>
        </button>
    </span>
</template>

<script>
    export default {
        name: "CierreConcurso",
        props: ['id', 'texto'],
        methods: {
            cerrar() {
                return this.$store.dispatch('concursos/concurso/cerrar', {id: this.id})
                    .then(() => {
                        this.$router.push({name: 'concursos'});
                    })
            },
        }
    }
</script>

<style scoped>

</style>
