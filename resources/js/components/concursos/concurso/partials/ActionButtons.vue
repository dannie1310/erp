<template>
    <div class="btn-group">
        <router-link :to="{ name: 'concurso-edit', params: {id: this.value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
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
        }
    }
</script>

<style scoped>

</style>
