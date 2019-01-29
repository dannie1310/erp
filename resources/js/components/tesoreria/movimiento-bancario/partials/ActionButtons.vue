<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <button @click="edit" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar"><i class="fa fa-pencil"></i></button>
        <button @click="destroy"  v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {
            destroy() {

                },
            show() {
                this.$router.push({name: 'movimiento-bancario-show', params: {id: this.value.id}});
            },
            edit() {
                this.$router.push({name: 'movimiento-bancario-edit', params: {id: this.value.id}});
            }
        }
    }
</script>