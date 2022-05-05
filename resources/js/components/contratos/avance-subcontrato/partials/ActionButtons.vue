<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'avance-subcontrato-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'avance-subcontrato-edit', params: {id: value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "avance-action-buttons",
        components: {},
        props: ['value'],
    }
</script>
