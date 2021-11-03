<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'avance-obra-show', params: {id: value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'avance-obra-edit', params: {id: value.id}}" v-if="$root.can('consultar_avance_obra')" type="button" class="btn btn-sm btn-outline-secondary" title="Editar">
           <i class="fa fa-pencil"></i>
       </router-link>
        <router-link  :to="{ name: 'avance-obra-aprobar', params: {id: value.id}}" v-if="value.aprobar" type="button" class="btn btn-sm btn-outline-success" title="Aprobar">
            <i class="fa fa-thumbs-o-up" />
        </router-link>
        <router-link  :to="{ name: 'avance-obra-revertir', params: {id: value.id}}" v-if="value.revertir" type="button" class="btn btn-sm btn-outline-danger" title="Revertir">
            <i class="fa fa-thumbs-down"></i>
        </router-link>
        <router-link  :to="{ name: 'avance-obra-delete', params: {id: value.id}}" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
    </div>
</template>
<script>
    export default {
        name: "avance-obra-buttons",
        components: {},
        props: ['value'],
        methods: {}
    }
</script>

<style scoped>
</style>
