<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'relacion-gasto-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link v-if="value.edit" :to="{ name: 'relacion-gasto-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "relacion-gastos-action-buttons",
        components: {},
        props: ['value'],
        methods: {

        }
    }
</script>
