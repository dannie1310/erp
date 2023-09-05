<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'documento-recurso-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link :to="{ name: 'documento-recurso-edit', params: {id: value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <router-link :to="{ name: 'documento-recurso-delete', params: {id: value.id}}" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-spinner fa-spin"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "documento-action-buttons",
        components: {},
        props: ['value'],
        methods: {

        }
    }
</script>
