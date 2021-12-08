<template>
    <div class="btn-group">
        <button v-if="value.edit" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <button v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
    </div>
</template>

<script>
    export default {
        name: "ActionButtons",
        components: {},
        props: ['value'],
        methods: {

        }
    }
</script>

<style scoped>

</style>
