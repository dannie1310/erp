<template>
    <div class="btn-group">
        <router-link :to="{ name: 'concurso-edit', params: {id: this.value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <button @click="cerrar" v-if="value.close" type="button" class="btn btn-sm btn-outline-danger" title="Cerrar Concurso"><i class="fa fa-lock"></i> </button>
    </div>
</template>
<script>
    export default {
        name: "concurso-buttons",
        components: {},
        props: ['value'],
        data()
        {
            return {
            }
        },

        methods: {
            cerrar() {
                return this.$store.dispatch('concursos/concurso/cerrar', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
        }
    }
</script>

<style scoped>

</style>
